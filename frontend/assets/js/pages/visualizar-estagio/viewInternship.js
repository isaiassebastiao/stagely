document.addEventListener('DOMContentLoaded', () => {
  function getInternshipIdFromURL() {
    const params = new URLSearchParams(window.location.search)
    return params.get('id')
  }

  function getInternshipAreaFromURL() {
    const params = new URLSearchParams(window.location.search)
    return params.get('area')
  }

  function getCourseFromURL() {
    const params = new URLSearchParams(window.location.search)
    return params.get('curso')
  }

  const daysMap = {
    1: 'Segunda',
    2: 'Terça',
    3: 'Quarta',
    4: 'Quinta',
    5: 'Sexta',
    6: 'Sábado'
  }

  async function loadInternship(internshipId, internshipArea, course) {
    try {
      const response = await fetch(`/stagely/backend/routes/adminRoutes.php?action=getEnterpriseInternshipInfo&id=${internshipId}&area=${internshipArea}&curso=${course}`)

      if (!response.ok) {
        throw new Error('Erro na requisição')
      }

      const result = await response.json()
      console.log(result)

      if (!result.success) {
        showAlert(result.message, 'error')
        return
      }

      const internship = result.data

      // HEADER - Nome da empresa
      document.querySelector('.header h3').textContent = internship.nome || '-'

      // Foto da empresa
      const initial = internship.nome ? internship.nome.charAt(0).toUpperCase() : '-'

      const photoEl = document.querySelector('.photo')
      const photoH4 = photoEl?.querySelector('h4')

      if (photoH4) {
        photoH4.textContent = internship.imagem_perfil ? '' : initial
      }

      if (internship.imagem_perfil && photoEl) {
        photoEl.style.backgroundImage = `url('${internship.imagem_perfil}')`
      }

      // STATUS
      const statusEl = document.querySelector('.status span')
      const statusBox = document.querySelector('.status')

      const isActive = internship.status === 'Em execução'
      const isFinished = internship.status === 'Concluído'

      statusEl.textContent = internship.status || '-'

      statusBox.classList.remove('progress', 'finished', 'pending')
      if (isActive) {
        statusBox.classList.add('progress')
      } else if (isFinished) {
        statusBox.classList.add('finished')
      } else {
        statusBox.classList.add('pending')
      }

      // INFO
      const items = document.querySelectorAll('.info .item:not(.full-width) p')

      // Formatar período
      const periodo = internship.inicio && internship.fim ? `${internship.inicio.split('-').reverse().join('/')} - ${internship.fim.split('-').reverse().join('/')}` : '-'

      // Formatar dias
      const dias = internship.days && internship.days.length > 0 ? internship.days.map((d) => daysMap[d.id] || d.dia).join(', ') : '-'

      // Formatar horário
      const horario = internship.shifts && internship.shifts.length > 0 ? internship.shifts.map((h) => `${h.entrada.slice(0, 5)} - ${h.saida.slice(0, 5)}`).join(', ') : '-'

      const data = [internship.areas.map((a) => a.nome).join(', ') || '-', periodo, dias, horario]

      items.forEach((el, index) => {
        el.textContent = data[index] || '-'
      })

      // Preencher alunos na lista
      const alunosElement = document.querySelector('.info .item.full-width p')
      if (alunosElement) {
        const alunos = internship.interners && internship.interners.length > 0 ? internship.interners.map((a) => a.nome).join(', ') : '-'
        alunosElement.textContent = alunos
      }
    } catch (error) {
      console.error(error)
      showAlert('Erro ao carregar estágio.', 'error')
    }
  }

  const internshipId = getInternshipIdFromURL()
  const internshipArea = getInternshipAreaFromURL()
  const course = getCourseFromURL()

  if (internshipId) {
    loadInternship(internshipId, internshipArea, course)
  } else {
    showAlert('ID do estágio não encontrado.', 'error')
  }
})
