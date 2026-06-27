;(() => {
  const areFieldsFilled = (...fields) => fields.every((field) => field !== '' && field !== null)

  const getLoaderDelay = (startTime, minimum = 500) => {
    const elapsed = Date.now() - startTime
    return elapsed < minimum ? minimum - elapsed : 0
  }

  function validateDatesAndTimes(date_start, date_end, time_entry, time_out) {
    const startDate = new Date(date_start)
    const endDate = new Date(date_end)

    if (endDate < startDate) {
      showAlert('A data de fim não pode ser anterior à data de início', 'error')
      return false
    }

    if (time_out <= time_entry) {
      showAlert('A hora de saída deve ser maior que a hora de entrada', 'error')
      return false
    }

    return true
  }

  function getEnterpriseIdFromURL() {
    const params = new URLSearchParams(window.location.search)
    return params.get('id')
  }

  function getEnterpriseAreaFromURL() {
    const params = new URLSearchParams(window.location.search)
    return params.get('area')
  }

  function getCourseFromURL() {
    const params = new URLSearchParams(window.location.search)
    return params.get('curso')
  }

  var reqArea = null
  var status = null

  async function loadInternship() {
    const id = getEnterpriseIdFromURL()
    const area = getEnterpriseAreaFromURL()
    const course = getCourseFromURL()

    if (!id) {
      showAlert('Estágio não encontrado', 'error')
      return
    }

    try {
      const response = await fetch(`/stagely/backend/routes/adminRoutes.php?action=getEnterpriseInternshipInfo&id=${id}&area=${area}&curso=${course}`)
      const result = await response.json()
      console.log(result)

      if (!result.success) {
        showAlert(result.message, 'error')
        return
      }

      const internship = result.data
      reqArea = result.data.areas[0].area_id

      // Texto
      document.querySelector('[data-dropdown-toggle=data-enterprise] span').innerText = internship.nome
      //document.getElementById('area_internship').disabled = true
      status = internship.status

      if (internship.status.toLowerCase() === 'em execução') document.getElementById('date_start').disabled = true

      // Preencher campos
      document.getElementById('enterprise').value = internship.id || ''
      document.getElementById('area_internship').value = internship.areas[0].nome || ''
      document.getElementById('date_start').value = internship.inicio || ''
      document.getElementById('date_fim').value = internship.fim || ''
      document.getElementById('time_entry').value = internship.shifts[0].entrada || ''
      document.getElementById('time_out').value = internship.shifts[0].saida || ''

      // Dias
      if (internship.days && Array.isArray(internship.days)) {
        internship.days.forEach((day) => {
          const checkbox = document.querySelector(`input[name="days[]"][value="${day.id}"]`)
          if (checkbox) checkbox.checked = true
        })
      }

      // Alunos
      if (internship.interners && Array.isArray(internship.interners)) {
        const studentsIds = internship.interners.map((a) => a.id).join(',')
        document.getElementById('students').value = studentsIds
        updateStudentsText()
      }

      updateEnterpriseText()

      if (window.initializeCustomSelects) {
        window.initializeCustomSelects()
      }
    } catch (error) {
      console.error(error)
      showAlert('Erro ao carregar estágio', 'error')
    }
  }

  function updateEnterpriseText() {
    const enterprise = document.getElementById('enterprise').value
    const text = document.querySelector('[data-dropdown-toggle="data-enterprise"] .text')

    if (enterprise && text) {
      const selectedOption = document.querySelector(`[data-dropdown-toggle="data-enterprise"] + .options ul li[data-value="${enterprise}"]`)

      if (selectedOption) {
        text.textContent = selectedOption.textContent
      }
    }
  }

  function updateStudentsText() {
    const students = document.getElementById('students').value
    const text = document.querySelector('[data-dropdown-toggle="data-students"] .text')

    if (students && text) {
      const values = students
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean)
      if (values.length === 1) {
        const option = document.querySelector(`#data-students .item[data-value="${values[0]}"]`)
        text.textContent = option ? option.textContent.trim() : '1 selecionado(a)'
      } else {
        text.textContent = values.length > 0 ? `${values.length} selecionado(s)` : 'Selecionar'
      }
    }
  }

  async function updateInternship(event) {
    event.preventDefault()

    const id = getEnterpriseIdFromURL()
    const area = getEnterpriseAreaFromURL()
    const course = getCourseFromURL()

    const studentsReq = await fetch(`/stagely/backend/routes/adminRoutes.php?action=enterpriseInterners&id=${id}&area=${area}&curso=${course}`)
    const studentsRes = await studentsReq.json()
    const studentsRequestArray = studentsRes.data.map((s) => s.id)

    if (!id) {
      showAlert('Estágio não encontrado', 'error')
      return
    }

    const form = event.target
    const submitButton = form.querySelector('button[type="submit"]')

    const enterprise = document.getElementById('enterprise').value.trim()
    const area_internship = document.getElementById('area_internship').value.trim()

    const studentsInput = document.getElementById('students')
    let studentsArray = []

    if (studentsInput && studentsInput.value) {
      studentsArray = studentsInput.value
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean)
    }

    const date_start = document.getElementById('date_start').value.trim()
    const date_end = document.getElementById('date_fim').value.trim()
    const time_entry = document.getElementById('time_entry').value.trim()
    const time_out = document.getElementById('time_out').value.trim()

    const daysCheckboxes = Array.from(form.querySelectorAll('input[name="days[]"]:checked'))
    const days = daysCheckboxes.map((cb) => cb.value)

    if (!areFieldsFilled(enterprise, area_internship, date_start, date_end, time_entry, time_out) || studentsArray.length === 0 || days.length === 0) {
      showAlert('Preencha todos os campos', 'error')
      return
    }

    if (!validateDatesAndTimes(date_start, date_end, time_entry, time_out)) {
      return
    }

    submitButton.disabled = true
    submitButton.classList.add('loading')

    const startTime = Date.now()

    const payload = {
      id,
      enterprise,
      area_internship,
      students: studentsArray,
      date_start,
      date_end,
      time_entry,
      time_out,
      days,
      receivedStudents: studentsRequestArray,
      course: course,
      oldAreaId: reqArea,
      status
    }

    try {
      const response = await fetch('/stagely/backend/routes/adminRoutes.php?action=updateInternship', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })

      const result = await response.json()
      console.log(result)
      const waitTime = getLoaderDelay(startTime)

      setTimeout(() => {
        submitButton.disabled = false
        submitButton.classList.remove('loading')

        if (result.success) {
          showAlert(result.message, 'success')

          setTimeout(() => {
            window.location.href = `/stagely/frontend/pages/listar-estagios/index.php?curso=${getCourseFromURL()}&dropdown=internship`
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
    loadInternship()
  })

  window.updateInternship = updateInternship
})()
