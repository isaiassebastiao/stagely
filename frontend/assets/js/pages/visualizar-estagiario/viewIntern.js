document.addEventListener('DOMContentLoaded', () => {
  function getInternIdFromURL() {
    const params = new URLSearchParams(window.location.search)
    return params.get('id')
  }

  const daysMap = {
    1: 'Segunda',
    2: 'Terça',
    3: 'Quarta',
    4: 'Quinta',
    5: 'Sexta',
    6: 'Sábado'
  }

  async function loadIntern(internId) {
    try {
      const response = await fetch(`/stagely/backend/routes/enterpriseRoutes.php?action=getInternerInfo&id=${internId}`)

      if (!response.ok) {
        throw new Error('Erro na requisição')
      }

      const result = await response.json()
      console.log(result)

      if (!result.success) {
        showAlert(result.message, 'error')
        return
      }

      const intern = result.data

      // HEADER - Nome da empresa
      document.querySelector('.header h3').textContent = intern.nome || '-'

      // Foto da empresa
      const initial = intern.nome ? intern.nome.charAt(0).toUpperCase() : '-'

      const photoEl = document.querySelector('.photo')
      const photoH4 = photoEl?.querySelector('h4')

      if (photoH4) {
        photoH4.textContent = intern.imagem_perfil ? '' : initial
      }

      if (intern.imagem_perfil && photoEl) {
        photoEl.style.backgroundImage = `url('${intern.imagem_perfil}')`
      }

      // STATUS
      const statusEl = document.querySelector('.status span')
      const statusBox = document.querySelector('.status')

      const isActive = intern.status === 'Em execução'
      const isFinished = intern.status === 'Concluído'

      statusEl.textContent = intern.status || '-'

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
      const periodo = intern.inicio && intern.fim ? `${intern.inicio.split('-').reverse().join('/')} - ${intern.fim.split('-').reverse().join('/')}` : '-'

      // Formatar dias
      const dias = intern.days && intern.days.length > 0 ? intern.days.map((d) => daysMap[d.id] || d.dia).join(', ') : '-'

      //Curso
      const curso = intern.curso

      // Formatar horário
      const horario = `${intern.shift.entrada.slice(0, 5)} - ${intern.shift.saida.slice(0, 5)}`

      const data = [curso, intern.areas || '-', periodo, dias, horario]

      items.forEach((el, index) => {
        el.textContent = data[index] || '-'
      })

      // Preencher alunos na lista
      const alunosElement = document.querySelector('.info .item.full-width p')
      if (alunosElement) {
        const alunos = intern.interners && intern.interners.length > 0 ? intern.interners.map((a) => a.nome).join(', ') : '-'
        alunosElement.textContent = alunos
      }
    } catch (error) {
      console.error(error)
      showAlert('Erro ao carregar estagiário.', 'error')
    }
  }

  const internId = getInternIdFromURL()

  if (internId) {
    loadIntern(internId)
  } else {
    showAlert('ID do estagiário não encontrado.', 'error')
  }
})
