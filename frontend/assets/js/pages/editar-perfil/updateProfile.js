;(() => {
  const areFieldsFilled = (...fields) => fields.every((field) => field !== '' && field !== null)
  const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
  const getLoaderDelay = (startTime, minimum = 500) => {
    const elapsed = Date.now() - startTime
    return elapsed < minimum ? minimum - elapsed : 0
  }

  async function loadProfile() {
    try {
      const response = await fetch('/stagely/backend/routes/userRoutes.php?action=getProfile')
      const result = await response.json()
      console.log(result)
      if (result.success) {
        const data = result
        document.getElementById('name').value = data.name || ''
        document.getElementById('email').value = data.email || ''
        const photo = document.querySelector('.photo')
        const photoPreview = document.getElementById('photoPreview')
        if (data.profile_pic) {
          if (photo) photo.style.backgroundImage = `url('${data.profile_pic}')`
          if (photoPreview) photoPreview.src = data.profile_pic
        }
      } else {
        showAlert('Empresa não encontrada', 'error')
      }
    } catch (error) {
      console.error(error)
      showAlert('Não foi possível carregar os dados do perfil', 'error')
    }
  }

  async function updateProfile(event) {
    event.preventDefault()

    const form = event.target
    const submitButton = form.querySelector('button[type="submit"]')
    const name = document.getElementById('name').value.trim()
    const email = document.getElementById('email').value.trim()
    const fileInput = document.getElementById('photoInput')

    if (!areFieldsFilled(name, email)) {
      showAlert('Os campos nome e e-mail não podem estar vazios', 'error')
      return
    }
    if (!isValidEmail(email)) {
      showAlert('Formato de e-mail inválido', 'error')
      return
    }

    submitButton.disabled = true
    submitButton.classList.add('loading')
    const startTime = Date.now()

    const formData = new FormData()
    formData.append('name', name)
    formData.append('email', email)
    if (fileInput.files[0]) formData.append('photo', fileInput.files[0])

    try {
      const response = await fetch('/stagely/backend/routes/adminRoutes.php?action=editUser', {
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
        } else {
          showAlert(result.message || 'Erro ao atualizar perfil', 'error')
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

  window.updateProfile = updateProfile
  loadProfile()
})()
