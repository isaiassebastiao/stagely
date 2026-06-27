document.addEventListener('DOMContentLoaded', async () => {
  try {
    const yearsList = document.querySelector('#data-filter .list')

    const response = await fetch('/stagely/backend/routes/internshipInfo.php?action=staticData')
    const result = await response.json()

    const years = result.years || []

    yearsList.innerHTML = ''

    const allLi = document.createElement('li')
    allLi.className = 'item active'
    allLi.dataset.year = 'todos'
    allLi.textContent = 'Todos'
    yearsList.appendChild(allLi)

    years.forEach((y) => {
      const li = document.createElement('li')
      li.className = 'item'
      li.dataset.year = y.year
      li.textContent = y.year
      yearsList.appendChild(li)
    })
  } catch (error) {
    console.error('Erro ao carregar filtros:', error)
  }
})
