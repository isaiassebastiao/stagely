async function fillFilters(stats = null, filterType = 'course') {
  try {
    const yearsList = document.querySelector('#data-filter .list')
    const studentsList = document.querySelector('#data-course-students .list')
    const internshipsList = document.querySelector('#data-course-internships .list')

    if (!yearsList || !studentsList || !internshipsList) return

    let areas = []
    let courses = []
    let internersStats = []
    let internshipStats = []

    if (stats) {
      areas = stats.areas || []
      courses = stats.courses || []
      internersStats = stats.internersStats || []
      internshipStats = stats.internshipStats || []
    } else {
      const getRole = await fetch('/stagely/backend/routes/getRole.php')
      const role = await getRole.json()

      const ans = role.role === 'Escola' ? await fetch('/stagely/backend/routes/adminRoutes.php?action=generalStats') : await fetch('/stagely/backend/routes/enterpriseRoutes.php?action=generalStats')
      const fetchedStats = await ans.json()

      areas = fetchedStats.areas || []
      courses = fetchedStats.courses || []
      internersStats = fetchedStats.internersStats || []
      internshipStats = fetchedStats.internshipStats || []
    }

    yearsList.innerHTML = ''

    const allYearsLi = document.createElement('li')
    allYearsLi.className = 'item active'
    allYearsLi.dataset.year = 'todos'
    allYearsLi.textContent = 'Todos'
    yearsList.appendChild(allYearsLi)

    const uniqueYears = [...new Set([...internersStats.map((i) => i.year), ...internshipStats.map((i) => i.year)].filter(Boolean))].sort()

    uniqueYears.forEach((year) => {
      const li = document.createElement('li')
      li.className = 'item'
      li.dataset.year = year
      li.textContent = year
      yearsList.appendChild(li)
    })

    const studentsSourceList = filterType === 'area' ? areas : courses
    const internshipsSourceList = filterType === 'area' ? areas : courses

    studentsList.innerHTML = ''

    const allStudentsLi = document.createElement('li')
    allStudentsLi.className = 'item active'
    allStudentsLi.dataset.value = 'todos'
    allStudentsLi.textContent = 'Todos'
    studentsList.appendChild(allStudentsLi)

    studentsSourceList.forEach((item) => {
      const li = document.createElement('li')
      li.className = 'item'
      li.dataset.value = item.value || item.slug || item.nome || item.name
      li.textContent = item.label || item.nome || item.name
      studentsList.appendChild(li)
    })

    internshipsList.innerHTML = ''

    const allInternshipsLi = document.createElement('li')
    allInternshipsLi.className = 'item active'
    allInternshipsLi.dataset.value = 'todos'
    allInternshipsLi.textContent = 'Todos'
    internshipsList.appendChild(allInternshipsLi)

    internshipsSourceList.forEach((item) => {
      const li = document.createElement('li')
      li.className = 'item'
      li.dataset.value = item.value || item.slug || item.nome || item.name
      li.textContent = item.label || item.nome || item.name
      internshipsList.appendChild(li)
    })
  } catch (error) {
    console.error('Erro ao carregar filtros', error)
  }
}
