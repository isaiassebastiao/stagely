;(() => {
  const areFieldsFilled = (...fields) => fields.every((field) => field !== '' && field !== null)

  const getLoaderDelay = (startTime, minimum = 500) => {
    const elapsed = Date.now() - startTime
    return elapsed < minimum ? minimum - elapsed : 0
  }

  async function updatePassword(event) {
    event.preventDefault()

    const form = event.target
    const submitButton = form.querySelector('button[type="submit"]')

    const currentPassword = form.current_password.value.trim()
    const newPassword = form.new_password.value.trim()
    const confirmPassword = form.confirm_password.value.trim()

    // Validação de campos obrigatórios
    if (!areFieldsFilled(currentPassword, newPassword, confirmPassword)) {
      showAlert('Preencha todos os campos', 'error')
      return
    }

    if (newPassword !== confirmPassword) {
      showAlert('A nova senha e a confirmação não coincidem', 'error')
      return
    }

    if (newPassword.length < 8) {
      showAlert('A nova senha deve ter pelo menos 8 caracteres', 'error')
      return
    }

    // Ativa loader
    submitButton.disabled = true
    submitButton.classList.add('loading')
    const startTime = Date.now()

    const payload = {
      current_password: currentPassword,
      new_password: newPassword
    }

    try {
      const response = await fetch('/stagely/backend/routes/userRoutes.php?action=changePassword', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })

      const result = await response.json()
      const waitTime = getLoaderDelay(startTime)

      setTimeout(() => {
        submitButton.disabled = false
        submitButton.classList.remove('loading')

        if (result.success) {
          showAlert(result.message, 'success')
          form.reset()
        } else {
          showAlert(result.message, 'error')
        }
      }, waitTime)
    } catch (error) {
      console.error(error)
      const waitTime = getLoaderDelay(startTime)
      setTimeout(() => {
        submitButton.disabled = false
        submitButton.classList.remove('loading')
        showAlert('Erro de conexão. Tente novamente', 'error')
      }, waitTime)
    }
  }

  // Limpa o formulário e desativa o loader quando a página carrega
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form')
    const submitButton = form?.querySelector('button[type="submit"]')

    if (form && typeof form.reset === 'function') form.reset()

    if (submitButton) {
      submitButton.disabled = false
      submitButton.classList.remove('loading')
    }
  })

  // Evita cache e recarrega se a página voltar do histórico
  window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
      window.location.reload()
    }
  })

  window.updatePassword = updatePassword
})()
