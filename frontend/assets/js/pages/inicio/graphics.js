document.addEventListener('DOMContentLoaded', () => {
  ; (async () => {
    try {
      const response = await fetch('/stagely/backend/routes/getRole.php')
      const result = await response.json()
      const role = result.role

      const statsResponse = role === "Escola" ? await fetch('/stagely/backend/routes/adminRoutes.php?action=generalStats') :
        await fetch("/stagely/backend/routes/enterpriseRoutes.php?action=generalStats")

      const stats = await statsResponse.json()
      console.log(stats)

      const { internersStats = [], internshipStats = [] } = stats

      function countByStatus(array) {
        const result = {
          pending: 0,
          running: 0,
          finished: 0
        }

        array.forEach((item) => {
          const status = item.status?.toLowerCase().trim()

          if (status === 'pendente') result.pending++
          else if (status === 'em execução' || status === 'em execucao') result.running++
          else if (status === 'concluído' || status === 'concluido') result.finished++
        })

        return result
      }

      const studentsCardTitle = document.querySelector('.cards .card:nth-child(1) .card-header h3')
      const studentsCenterText = document.querySelector('.cards .card:nth-child(1) .chart-center span')
      const studentsCenterValue = document.querySelector('.cards .card:nth-child(1) .chart-center h4')
      const studentsFilterText = document.querySelector('[data-dropdown-toggle="data-course-students"] .text')

      const internshipsFilterText = document.querySelector('[data-dropdown-toggle="data-course-internships"] .text')
      const internshipsCenterText = document.querySelector('.cards .card:nth-child(2) .chart-center span')
      const internshipsCenterValue = document.querySelector('.cards .card:nth-child(2) .chart-center h4')

      const totalStageEl = document.querySelector('.stats .stat:nth-child(1) h4')
      const pendingStageEl = document.querySelector('.stats .stat:nth-child(2) h4')
      const runningStageEl = document.querySelector('.stats .stat:nth-child(3) h4')
      const finishedStageEl = document.querySelector('.stats .stat:nth-child(4) h4')

      const studentsLegend = document.querySelector('.cards .card:nth-child(1) .legend')
      const internshipsLegend = document.querySelector('.cards .card:nth-child(2) .legend')

      if (!studentsCardTitle) return

      let studentsData = {}
      let internshipsData = {}
      let filterType = ''

      if (role === 'Escola') {
        studentsCardTitle.textContent = 'Alunos'
        studentsCenterText.textContent = 'Alunos'
        studentsFilterText.textContent = 'Curso'
        internshipsFilterText.textContent = 'Curso'
        internshipsCenterText.textContent = 'Estágios'
        filterType = 'course'
      } else if (role === 'Empresa') {
        studentsCardTitle.textContent = 'Estagiários'
        studentsCenterText.textContent = 'Estagiários'
        studentsFilterText.textContent = 'Área'
        internshipsFilterText.textContent = 'Área'
        internshipsCenterText.textContent = 'Estágios'
        filterType = 'area'
      }

      document.body.dataset.filterType = filterType

      await fillFilters({ internersStats, internshipStats, areas: stats.areas || [], courses: stats.courses || [] }, filterType)

      const getActiveFilterValue = (dropdownId) => {
        const activeItem = document.querySelector(`#${dropdownId} .item.active`)
        return activeItem?.dataset.value?.toString().trim() || 'todos'
      }

      const getActiveYear = () =>
        document.querySelector('#data-filter .item.active')?.dataset.year?.toString().trim() || 'todos'

      const normalizeValue = (value) =>
        value?.toString()?.trim().toLowerCase() || ''

      const getFieldValue = (item, fields) => {
        for (const field of fields) {
          if (item[field] !== undefined && item[field] !== null) {
            return item[field]
          }
        }
        return ''
      }

      const filterItems = (items, filterType, selectedValue, selectedYear) => {
        const normalizedYear = normalizeValue(selectedYear)
        const normalizedValue = normalizeValue(selectedValue)

        return items.filter((item) => {
          const itemYear = normalizeValue(getFieldValue(item, ['year', 'ano', 'ano_letivo', 'year_lective', 'year']))
          const itemCourse = normalizeValue(getFieldValue(item, ['curso', 'course', 'nome', 'name']))
          const itemArea = normalizeValue(getFieldValue(item, ['area', 'area_estagio', 'area_activity', 'field', 'ramo_atuacao', 'ramos_atuacao', 'nome']))
          const hasArea = itemArea !== ''
          const hasCourse = itemCourse !== ''

          if (normalizedYear && normalizedYear !== 'todos' && itemYear !== normalizedYear) {
            return false
          }

          if (normalizedValue && normalizedValue !== 'todos') {
            if (filterType === 'course') {
              if (hasCourse && itemCourse !== normalizedValue) {
                return false
              }
            }
            if (filterType === 'area') {
              if (hasArea && itemArea !== normalizedValue) {
                return false
              }
            }
          }

          return true
        })
      }

      const colors = ['#f59e0b', '#7132f5', '#10b981']

      const studentsChart = new Chart(document.getElementById('studentsChart'), {
        type: 'doughnut',
        data: {
          labels: ['Pendentes', 'Em estágio', 'Concluídos'],
          datasets: [
            {
              data: [0, 0, 0],
              backgroundColor: colors,
              borderWidth: 0
            }
          ]
        },
        options: {
          cutout: '80%',
          responsive: true,
          plugins: { legend: { display: false } }
        }
      })

      const internshipsChart = new Chart(document.getElementById('internshipsChart'), {
        type: 'doughnut',
        data: {
          labels: ['Pendentes', 'Em execução', 'Concluídos'],
          datasets: [
            {
              data: [0, 0, 0],
              backgroundColor: colors,
              borderWidth: 0
            }
          ]
        },
        options: {
          cutout: '80%',
          responsive: true,
          plugins: { legend: { display: false } }
        }
      })

      const updateDashboard = () => {
        const selectedYear = getActiveYear()
        const selectedStudentsValue = getActiveFilterValue('data-course-students')
        const selectedInternshipsValue = getActiveFilterValue('data-course-internships')

        const filteredStudents = filterItems(internersStats, filterType, selectedStudentsValue, selectedYear)
        const filteredInternships = filterItems(internshipStats, filterType, selectedInternshipsValue, selectedYear)

        const studentsCounts = countByStatus(filteredStudents)
        const internshipsCounts = countByStatus(filteredInternships)

        studentsChart.data.datasets[0].data = [studentsCounts.pending, studentsCounts.running, studentsCounts.finished]
        studentsChart.update()
        studentsCenterValue.textContent = studentsCounts.pending + studentsCounts.running + studentsCounts.finished

        internshipsChart.data.datasets[0].data = [internshipsCounts.pending, internshipsCounts.running, internshipsCounts.finished]
        internshipsChart.update()
        internshipsCenterValue.textContent = internshipsCounts.pending + internshipsCounts.running + internshipsCounts.finished

        totalStageEl.textContent = internshipsCounts.pending + internshipsCounts.running + internshipsCounts.finished
        pendingStageEl.textContent = internshipsCounts.pending
        runningStageEl.textContent = internshipsCounts.running
        finishedStageEl.textContent = internshipsCounts.finished

        if (studentsLegend) {
          studentsLegend.innerHTML = `
            <div class="legend-item"><span class="dot orange"></span> ${studentsCounts.pending} Pendentes</div>
            <div class="legend-item"><span class="dot blue"></span> ${studentsCounts.running} Em estágio</div>
            <div class="legend-item"><span class="dot green"></span> ${studentsCounts.finished} Concluídos</div>
          `
        }

        if (internshipsLegend) {
          internshipsLegend.innerHTML = `
            <div class="legend-item"><span class="dot orange"></span> ${internshipsCounts.pending} Pendentes</div>
            <div class="legend-item"><span class="dot blue"></span> ${internshipsCounts.running} Em execução</div>
            <div class="legend-item"><span class="dot green"></span> ${internshipsCounts.finished} Concluídos</div>
          `
        }
      }

      document.body.addEventListener('click', (event) => {
        const item = event.target.closest('.options .item')
        if (!item) return

        const dropdown = item.closest('.options')
        if (!dropdown) return

        if (['data-filter', 'data-course-students', 'data-course-internships'].includes(dropdown.id)) {
          requestAnimationFrame(updateDashboard)
        }
      })

      updateDashboard()
    } catch (error) {
      console.error('Erro ao carregar dashboard:', error)
    }
  })()
})
