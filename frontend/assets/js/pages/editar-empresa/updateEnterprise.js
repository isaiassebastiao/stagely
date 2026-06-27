;(() => {
  const areFieldsFilled = (...fields) => fields.every((field) => field !== '' && field !== null)
  const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
  const isVacanciesValid = (vacancies) => /^[1-9]\d*$/.test(vacancies)
  const getLoaderDelay = (startTime, minimum = 500) => {
    const elapsed = Date.now() - startTime
    return elapsed < minimum ? minimum - elapsed : 0
  }

  function getEnterpriseIdFromURL() {
    const params = new URLSearchParams(window.location.search)
    return params.get('id')
  }

  async function loadEnterprise(enterpriseId) {
    try {
      const response = await fetch(`/stagely/backend/routes/adminRoutes.php?action=getEnterprise&id=${enterpriseId}`)
      const ans = await fetch('/stagely/backend/routes/adminRoutes.php?action=registerInfo')
      const registerValues = await ans.json()
      const result = await response.json()

      if (result.success) {
        const data = result.data

        document.getElementById('name').value = data.name || ''
        document.getElementById('email').value = data.email || ''
        document.getElementById('vacancies').value = data.vacancies || ''
        document.getElementById('hood').value = data.hood || ''
        document.getElementById('street').value = data.street || ''

        document.getElementById('area_activity').value = data.area_activity?.map((a) => a.field_id).join(',') || ''

        document.querySelector('#data-area-activity .list').innerHTML = registerValues.actingArea
          .map(
            (a) =>
              `<li class="item item-area" data-value="${a.id}" data-area="${a.nome}">
                  <div class="area-thumbnail"></div>${a.nome}
                </li>`
          )
          .join('')

        const areaSelect = document.querySelector('[data-dropdown-toggle="data-area-activity"] .text')
        if (areaSelect) {
          areaSelect.textContent = data.area_activity?.map((a) => a.area).join(', ') || 'Selecionar'
        }

        if (window.initializeCustomSelects) {
          window.initializeCustomSelects()
        }

        // Foto
        const photo = document.querySelector('.photo')
        const photoPreview = document.getElementById('photoPreview')

        if (data.photo) {
          if (photo) photo.style.backgroundImage = `url('${data.photo}')`
          if (photoPreview) photoPreview.src = data.photo
        }
      } else {
        showAlert('Empresa não encontrada', 'error')
      }
    } catch (error) {
      console.error(error)
      showAlert('Erro ao carregar os dados da empresa', 'error')
    }
  }

  async function updateEnterprise(event) {
    event.preventDefault()

    const form = event.target
    const submitButton = form.querySelector('button[type="submit"]')
    const enterpriseId = getEnterpriseIdFromURL()

    if (!enterpriseId) {
      showAlert('ID da empresa não encontrado', 'error')
      return
    }

    const name = document.getElementById('name').value.trim()
    const email = document.getElementById('email').value.trim().toLowerCase()
    const password = document.getElementById('password').value.trim()

    const areaActivityInput = document.getElementById('area_activity')
    const area_activity = areaActivityInput.value
      ? areaActivityInput.value
          .split(',')
          .map((item) => item.trim())
          .filter(Boolean)
      : []

    const vacancies = document.getElementById('vacancies').value.trim()
    const hood = document.getElementById('hood').value.trim()
    const street = document.getElementById('street').value.trim()

    const fileInput = form.querySelector('#photoInput')

    if (!areFieldsFilled(name, email, vacancies, hood, street) || area_activity.length === 0) {
      showAlert('Preencha todos os campos', 'error')
      return
    }

    if (!isValidEmail(email)) {
      showAlert('Formato de e-mail inválido', 'error')
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
    formData.append('id', enterpriseId)
    formData.append('name', name)
    formData.append('email', email)

    if (password) formData.append('password', password)

    formData.append('vacancies', vacancies)
    formData.append('hood', hood)
    formData.append('street', street)

    area_activity.forEach((item) => formData.append('area_activity[]', item))

    if (fileInput?.files?.length > 0) {
      formData.append('photo', fileInput.files[0])
    }

    try {
      const response = await fetch('/stagely/backend/routes/adminRoutes.php?action=editEnterprise', {
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

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form')
    const submitButton = form?.querySelector('button[type="submit"]')
    const enterpriseId = getEnterpriseIdFromURL()

    if (enterpriseId) loadEnterprise(enterpriseId)

    if (form) form.addEventListener('submit', updateEnterprise)

    if (submitButton) {
      submitButton.disabled = false
      submitButton.classList.remove('loading')
    }
  })

  window.updateEnterprise = updateEnterprise
})()
