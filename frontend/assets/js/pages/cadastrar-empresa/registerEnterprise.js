;(() => {
  const areFieldsFilled = (...fields) => fields.every((field) => field !== '')
  const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
  const isPasswordValid = (password) => password.length >= 8
  const isVacanciesValid = (vacancies) => /^[1-9]\d*$/.test(vacancies)
  const getLoaderDelay = (startTime, minimum = 500) => {
    const elapsed = Date.now() - startTime
    return elapsed < minimum ? minimum - elapsed : 0
  }

  async function registerEnterprise(event) {
    event.preventDefault()

    const form = event.target

    const fileInput = form.querySelector('#photoInput')
    const submitButton = form.querySelector('button[type="submit"]')

    const name = document.getElementById('name').value.trim()
    const email = document.getElementById('email').value.trim().toLowerCase()
    const password = document.getElementById('password').value.trim()
    const areaActivityInput = document.querySelector('#area_activity')
    let area_activity = []

    if (areaActivityInput && areaActivityInput.value) {
      area_activity = areaActivityInput.value
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean)
    }

    const vacancies = document.getElementById('vacancies').value.trim()
    const hood = document.getElementById('hood').value.trim()
    const street = document.getElementById('street').value.trim()

    if (!areFieldsFilled(name, email, password, vacancies, hood, street) || area_activity.length === 0) {
      showAlert('Preencha todos os campos', 'error')
      return
    }

    if (!isValidEmail(email)) {
      showAlert('Formato de e-mail inválido', 'error')
      return
    }

    if (!isPasswordValid(password)) {
      showAlert('Senha mínima 8 caracteres', 'error')
      return
    }

    if (!isVacanciesValid(vacancies)) {
      showAlert('Número de vagas inválido', 'error')
      return
    }

    submitButton.disabled = true
    submitButton.classList.add('loading')

    const startTime = Date.now()

    const formData = new FormData()
    formData.append('name', name)
    formData.append('email', email)
    formData.append('password', password)
    formData.append('vacancies', vacancies)
    formData.append('hood', hood)
    formData.append('street', street)

    area_activity.forEach((item) => formData.append('area_activity[]', item))

    if (fileInput?.files?.length > 0) {
      formData.append('photo', fileInput.files[0])
    }

    try {
      const response = await fetch('/stagely/backend/routes/adminRoutes.php?action=addEnterprise', {
        method: 'POST',
        body: formData
      })

      const result = await response.json()
      const waitTime = getLoaderDelay(startTime)

      setTimeout(() => {
        submitButton.disabled = false
        submitButton.classList.remove('loading')

        if (result.success) {
          showAlert(result.message, 'success')
          setTimeout(() => {
            window.location.href = '/stagely/frontend/pages/listar-empresas/index.php'
          }, 1000)
        } else {
          setTimeout(() => {
            submitButton.disabled = false
            submitButton.classList.remove('loading')
            showAlert(result.message, 'error')
          }, waitTime)
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

  window.registerEnterprise = registerEnterprise
})()
