let confirmCallback = null

const modalTypes = {
  delete: {
    icon: '#trash',
    confirmText: 'Eliminar'
  },
  replace: {
    icon: '#proccess',
    confirmText: 'Substituir'
  }
}

function openConfirmModal({ title = 'Eliminar', message = 'Tem certeza que deseja continuar?', type = 'delete', icon, confirmText, cancelText = 'Cancelar', onConfirm }) {
  confirmCallback = onConfirm

  const typeConfig = modalTypes[type] || {}

  document.getElementById('title').textContent = title
  document.getElementById('message').textContent = message

  const iconElement = document.getElementById('confirmIcon')
  if (iconElement) {
    iconElement.setAttribute('href', icon || typeConfig.icon || '#trash')
  }

  const confirmButton = document.getElementById('confirmOk')
  if (confirmButton) {
    confirmButton.textContent = confirmText || typeConfig.confirmText || 'Confirmar'
    confirmButton.classList.remove('violet', 'danger')
  }

  const cancelButton = document.getElementById('confirmCancel')
  if (cancelButton) {
    cancelButton.textContent = cancelText
  }

  document.getElementById('confirmModal').classList.remove('hidden')
}

function closeConfirmModal() {
  confirmCallback = null
  document.getElementById('confirmModal').classList.add('hidden')
}

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('confirmCancel').addEventListener('click', closeConfirmModal)

  document.getElementById('confirmOk').addEventListener('click', () => {
    if (typeof confirmCallback === 'function') {
      confirmCallback()
    }
    closeConfirmModal()
  })
})
