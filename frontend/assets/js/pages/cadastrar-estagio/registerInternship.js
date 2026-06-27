;(() => {
  const areFieldsFilled = (...fields) => fields.every((field) => field !== '' && field !== null)
  const urlParam=new URLSearchParams(window.location.search)
  const course=urlParam.get("curso")

  const getLoaderDelay = (startTime, minimum = 500) => {
    const elapsed = Date.now() - startTime
    return elapsed < minimum ? minimum - elapsed : 0
  }

  function validateDatesAndTimes(date_start, date_end, time_entry, time_out) {
    const today = new Date()
    today.setHours(0, 0, 0, 0)

    const startDate = new Date(date_start)
    const endDate = new Date(date_end)

    if (startDate < today) {
      showAlert('A data de início não pode ser anterior à data de hoje', 'error')
      return false
    }

    if (endDate < today) {
      showAlert('A data de fim não pode ser anterior à data de hoje', 'error')
      return false
    }

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

  async function registerInternship(event) {
    event.preventDefault()

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

    // Ativa loader
    submitButton.disabled = true
    submitButton.classList.add('loading')
    const startTime = Date.now()

    const payload = {
      enterprise,
      area_internship,
      students: studentsArray,
      date_start,
      date_end,
      time_entry,
      time_out,
      days,
      course:course
    }
    console.log(payload)

    try {
      const response = await fetch('/stagely/backend/routes/adminRoutes.php?action=setInternship', {
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
          setTimeout(() => {
            window.location.href = `/stagely/frontend/pages/listar-estagios/index.php?curso=${course}&dropdown=internships`
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

  window.registerInternship = registerInternship
})()
