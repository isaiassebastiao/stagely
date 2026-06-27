;(() => {
  function showAlert(message, type = 'success', duration = 3000) {
    const alert = document.getElementById('alert-notification')
    const text = document.getElementById('alert-text')
    const icon = alert.querySelector('.icon svg use')

    text.textContent = message

    alert.classList.remove('success', 'error', 'warning')

    alert.classList.add(type)

    const ICONS = {
      success: '#success',
      error: '#error',
      warning: '#warning'
    }

    icon.setAttribute('href', ICONS[type] || '#success')

    alert.classList.add('show')

    if (alert._timeoutId) {
      clearTimeout(alert._timeoutId)
    }

    if (duration > 0) {
      alert._timeoutId = setTimeout(() => {
        closeAlert()
      }, duration)
    }
  }

  function closeAlert() {
    const alert = document.getElementById('alert-notification')
    alert.classList.remove('show')
  }

  window.showAlert = showAlert
  window.closeAlert = closeAlert
})()
