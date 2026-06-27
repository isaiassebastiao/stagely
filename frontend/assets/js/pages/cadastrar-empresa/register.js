;(() => {
  const form = document.getElementById('form')
  const fileInput = document.getElementById('photoInput')
  const submitButton = document.querySelector('button[type="submit"]')

  function checkFormFields(nome, email, password, vacancies, hood, street, status) {
    return nome !== '' && email !== '' && password !== '' && vacancies !== '' && hood !== '' && street !== '' && status !== ''
  }

  function isValidEmail(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailPattern.test(email)
  }

  function isPasswordValid(password) {
    return password.length >= 8
  }

  function isVacanciesValid(vacancies) {
    return /^[1-9]\d*$/.test(vacancies)
  }

  function getLoaderDelay(startTime, minimum = 500) {
    const elapsed = Date.now() - startTime
    return elapsed < minimum ? minimum - elapsed : 0
  }

  async function registerEnterprise(event) {
    event.preventDefault()

    const nome = document.getElementById('name').value.trim()
    const email = document.getElementById('email').value.trim().toLowerCase()
    const password = document.getElementById('password').value.trim()
    const vacancies = document.getElementById('vacancies').value.trim()
    const hood = document.getElementById('hood').value.trim()
    const street = document.getElementById('street').value.trim()
    const status = document.getElementById('status').value.trim()


    if (!checkFormFields(nome, email, password, vacancies, hood, street, status)) {
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
    formData.append('name', nome)
    formData.append('email', email)
    formData.append('password', password)
    formData.append('vacancies', vacancies)
    formData.append('hood', hood)
    formData.append('street', street)
    formData.append('status', status)
    if (fileInput.files.length > 0) {
      formData.append('photo', fileInput.files[0])
    }

    try {
      const response = await fetch('../../../backend/routes/services.php', {
        method: 'POST',
        body: formData
      })

      const result = await response.json()
      const waitTime = getLoaderDelay(startTime)

      setTimeout(() => {
        submitButton.disabled = false
        submitButton.classList.remove('loading')

        if (result.success) {
          showAlert('Empresa cadastrada com sucesso', 'success')
          form.reset()
        } else {
          showAlert('Erro ao cadastrar empresa', 'error')
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

  window.registerEnterprise = registerEnterprise
})()
