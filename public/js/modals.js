// ========== ОБЩИЕ ФУНКЦИИ ==========

// Загрузка участников через AJAX
async function loadParticipants(eventId, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = `<div class="text-center p-4 text-muted">🔄 Загрузка участников...</div>`;

    try {
        const response = await fetch(`/cabinet/events/participants/${eventId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (data.success && data.participants.length > 0) {
            return data.participants;
        } else {
            container.innerHTML = `<div class="text-center p-4 text-muted">😕 Нет участников</div>`;
            return [];
        }
    } catch (err) {
        console.error(err);
        container.innerHTML = `<div class="text-center p-4 text-danger">⚠️ Ошибка загрузки</div>`;
        return [];
    }
}

// Отрисовка участников
function renderParticipants(participantsList, containerId, countElementId, onSelectCallback) {
    const container = document.getElementById(containerId);
    const currentUserId = document.querySelector('meta[name="user-id"]')?.getAttribute('content') || 0;

    container.innerHTML = '';

    // Фильтруем себя
    const filteredList = participantsList.filter(p => p.id != currentUserId);
    document.getElementById(countElementId).innerText = filteredList.length;

    filteredList.forEach(participant => {
        const card = document.createElement('div');
        card.className = 'participant-card';
        card.dataset.userId = participant.id;
        card.setAttribute('title', participant.name);

        card.innerHTML = `
            <img src="${participant.avatar}" class="participant-avatar" alt="${participant.name}">
            <div class="participant-check">✓</div>
        `;

        card.addEventListener('click', (e) => {
            e.stopPropagation();
            const userId = parseInt(card.dataset.userId);
            onSelectCallback(userId, card);
        });

        container.appendChild(card);
    });
}

// Обновление счетчика выбранных
function updateSelectedCount(selectedArray, elementId, prefix = '✅ Выбрано: ') {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = `${prefix}${selectedArray.length}`;
    }
}

// Проверка активности кнопки отправки
function checkSendButton(selectedArray, sendBtnId, hasGift = true, giftId = null) {
    const sendBtn = document.getElementById(sendBtnId);
    if (!sendBtn) return;

    let isActive = selectedArray.length > 0;
    if (hasGift) {
        isActive = isActive && (giftId !== null);
    }

    sendBtn.disabled = !isActive;

    // Сбрасываем текст если был загрузкой
    if (!sendBtn.disabled && sendBtn.innerHTML.includes('⏳')) {
        sendBtn.innerHTML = sendBtn.dataset.originalText || sendBtn.innerHTML;
    }
}

// Сброс состояния модального окна
function resetModalState(selectedArray, sendBtnId, giftSelector = null) {
    // Сбрасываем выбранных пользователей
    selectedArray.length = 0;

    // Сбрасываем кнопку
    const sendBtn = document.getElementById(sendBtnId);
    if (sendBtn) {
        sendBtn.disabled = true;
        sendBtn.innerHTML = sendBtn.dataset.originalText || sendBtn.innerHTML;
    }

    // Сбрасываем выделение участников
    document.querySelectorAll('.participant-card').forEach(c => c.classList.remove('selected'));

    // Сбрасываем выделение подарков (если есть)
    if (giftSelector) {
        document.querySelectorAll(giftSelector).forEach(g => g.classList.remove('selected'));
    }
}

// Отправка AJAX запроса
async function sendAjaxRequest(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    });
    return response.json();
}

// Показ уведомления
function showNotification(message, isSuccess = true) {
    alert((isSuccess ? '✅ ' : '❌ ') + message);
}

// ========== ПОДАРКИ ==========
let giftEventId = null;
let giftParticipants = [];
let selectedGiftUsers = [];
let selectedGiftId = null;

const giftModal = new bootstrap.Modal(document.getElementById('giftModal'));

// Инициализация модалки подарков
function initGiftModal() {
    // Настройка кнопки
    const sendBtn = document.getElementById('sendGiftBtn');
    if (sendBtn) {
        sendBtn.dataset.originalText = sendBtn.innerHTML;
    }

    // Обработчики подарков
    document.querySelectorAll('.gift-item').forEach(gift => {
        gift.addEventListener('click', () => {
            const gid = parseInt(gift.dataset.giftId);
            document.querySelectorAll('.gift-item').forEach(g => g.classList.remove('selected'));
            gift.classList.add('selected');
            selectedGiftId = gid;
            checkSendButton(selectedGiftUsers, 'sendGiftBtn', true, selectedGiftId);
        });
    });

    // Кнопка отправки
    document.getElementById('sendGiftBtn')?.addEventListener('click', sendGiftHandler);

    // Сброс при закрытии
    document.getElementById('giftModal')?.addEventListener('hidden.bs.modal', () => {
        resetModalState(selectedGiftUsers, 'sendGiftBtn', '.gift-item');
        selectedGiftId = null;
        if (typeof updateGiftSelectedCount === 'function') updateGiftSelectedCount();
    });
}

// Открытие модалки подарков
async function openGiftModal(eventId, eventTitle) {
    giftEventId = eventId;
    document.getElementById('giftModalTitle').innerHTML = `🎁 Подарить подарок: ${eventTitle}`;

    // Сброс
    resetModalState(selectedGiftUsers, 'sendGiftBtn', '.gift-item');
    selectedGiftId = null;
    if (typeof updateGiftSelectedCount === 'function') updateGiftSelectedCount();

    // Загрузка участников
    giftParticipants = await loadParticipants(eventId, 'giftParticipantsList');
    renderParticipants(giftParticipants, 'giftParticipantsList', 'giftParticipantsCount', (userId, cardElement) => {
        const idx = selectedGiftUsers.indexOf(userId);
        if (idx === -1) {
            selectedGiftUsers.push(userId);
            cardElement.classList.add('selected');
        } else {
            selectedGiftUsers.splice(idx, 1);
            cardElement.classList.remove('selected');
        }
        updateGiftSelectedCount();
        checkSendButton(selectedGiftUsers, 'sendGiftBtn', true, selectedGiftId);
    });

    giftModal.show();
}

function updateGiftSelectedCount() {
    updateSelectedCount(selectedGiftUsers, 'selectedGiftUsersCount');
}

async function sendGiftHandler() {
    if (selectedGiftUsers.length === 0 || !selectedGiftId) return;

    const giftName = document.querySelector(`.gift-item[data-gift-id="${selectedGiftId}"]`)?.getAttribute('title') || 'подарок';
    const names = giftParticipants.filter(p => selectedGiftUsers.includes(p.id)).map(p => p.name);

    if (!confirm(`🎁 Подарить "${giftName}" участникам:\n${names.join(', ')}\n\nОтправить?`)) return;

    const sendBtn = document.getElementById('sendGiftBtn');
    sendBtn.disabled = true;
    sendBtn.innerHTML = '⏳ Отправка...';

    const result = await sendAjaxRequest('/cabinet/send-gift', {
        event_id: giftEventId,
        gift_id: selectedGiftId,
        to_user_ids: selectedGiftUsers
    });

    if (result.success) {
        showNotification(result.message, true);
        giftModal.hide();
    } else {
        showNotification(result.message, false);
        sendBtn.disabled = false;
        sendBtn.innerHTML = sendBtn.dataset.originalText;
    }
}

// ========== ПОДМИГИВАНИЯ ==========
let winkEventId = null;
let winkParticipants = [];
let selectedWinkUsers = [];

const winkModal = new bootstrap.Modal(document.getElementById('winkModal'));

// Инициализация модалки подмигиваний
function initWinkModal() {
    const sendBtn = document.getElementById('sendWinkBtn');
    if (sendBtn) {
        sendBtn.dataset.originalText = sendBtn.innerHTML;
    }

    document.getElementById('sendWinkBtn')?.addEventListener('click', sendWinkHandler);

    document.getElementById('winkModal')?.addEventListener('hidden.bs.modal', () => {
        resetModalState(selectedWinkUsers, 'sendWinkBtn');
        if (typeof updateWinkSelectedCount === 'function') updateWinkSelectedCount();
    });
}

// Открытие модалки подмигиваний
async function openWinkModal(eventId, eventTitle) {
    winkEventId = eventId;
    document.getElementById('winkModalTitle').innerHTML = `😉 Подмигнуть: ${eventTitle}`;

    resetModalState(selectedWinkUsers, 'sendWinkBtn');
    if (typeof updateWinkSelectedCount === 'function') updateWinkSelectedCount();

    winkParticipants = await loadParticipants(eventId, 'winkParticipantsList');
    renderParticipants(winkParticipants, 'winkParticipantsList', 'winkParticipantsCount', (userId, cardElement) => {
        const idx = selectedWinkUsers.indexOf(userId);
        if (idx === -1) {
            selectedWinkUsers.push(userId);
            cardElement.classList.add('selected');
        } else {
            selectedWinkUsers.splice(idx, 1);
            cardElement.classList.remove('selected');
        }
        updateWinkSelectedCount();
        checkSendButton(selectedWinkUsers, 'sendWinkBtn', false);
    });

    winkModal.show();
}

function updateWinkSelectedCount() {
    updateSelectedCount(selectedWinkUsers, 'selectedWinkUsersCount');
}

async function sendWinkHandler() {
    if (selectedWinkUsers.length === 0) return;

    const names = winkParticipants.filter(p => selectedWinkUsers.includes(p.id)).map(p => p.name);

    if (!confirm(`😉 Подмигнуть участникам:\n${names.join(', ')}\n\nОтправить?`)) return;

    const sendBtn = document.getElementById('sendWinkBtn');
    sendBtn.disabled = true;
    sendBtn.innerHTML = '⏳ Отправка...';

    const result = await sendAjaxRequest('/cabinet/send-wink', {
        event_id: winkEventId,
        to_user_ids: selectedWinkUsers
    });

    if (result.success) {
        showNotification(result.message, true);
        winkModal.hide();
    } else {
        showNotification(result.message, false);
        sendBtn.disabled = false;
        sendBtn.innerHTML = sendBtn.dataset.originalText;
    }
}

// ========== ИНИЦИАЛИЗАЦИЯ ==========
document.addEventListener('DOMContentLoaded', () => {
    // Инициализация модалок
    initGiftModal();
    initWinkModal();

    // Обработчики кнопок для подарков
    document.querySelectorAll('.gift-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            openGiftModal(btn.dataset.eventId, btn.dataset.eventTitle);
        });
    });

    // Обработчики кнопок для подмигиваний
    document.querySelectorAll('.wink-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            openWinkModal(btn.dataset.eventId, btn.dataset.eventTitle);
        });
    });
});
