;(() => {
  const areFieldsFilled = (...fields) => fields.every((field) => field !== '')

  const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)

  const getLoaderDelay = (startTime, minimum = 500) => {
    const elapsed = Date.now() - startTime
    return elapsed < minimum ? minimum - elapsed : 0
  }

  async function handleLogin(event) {
    event.preventDefault()

    const form = event.target
    const submitButton = form.querySelector('button[type="submit"]')

    const email = form.email.value.trim()
    const password = form.password.value.trim()

    if (!areFieldsFilled(email, password)) {
      showAlert('Preencha seu e-mail e senha', 'error')
      return
    }

    if (!isValidEmail(email)) {
      showAlert('Formato de e-mail inválido', 'error')
      return
    }

    submitButton.disabled = true
    submitButton.classList.add('loading')

    const startTime = Date.now()

    const payload = {
      email,
      password
    }

    try {
      const response = await fetch('/stagely/backend/routes/userRoutes.php?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(payload)
      })

      const result = await response.json()
      const waitTime = getLoaderDelay(startTime)

      if (!result.success) {
        setTimeout(() => {
          submitButton.disabled = false
          submitButton.classList.remove('loading')
          showAlert(result.message, 'error')
        }, waitTime)
        return
      }

      setTimeout(() => {
        if (result.role === 'Escola') {
          window.location.href = '/stagely/frontend/pages/inicio/index.php'
        } else if (result.role === 'Empresa') {
          window.location.href = '/stagely/frontend/pages/inicio/index.php'
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

  window.handleLogin = handleLogin
})()
