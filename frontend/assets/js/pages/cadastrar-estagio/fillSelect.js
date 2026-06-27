document.addEventListener('DOMContentLoaded', async () => {
  try {
    const studentsList = document.querySelector('#data-students .list')
    const enterpriseList = document.querySelector('#data-enterprise .list')

    const urlParams = new URLSearchParams(window.location.search)
    const courseSelected = urlParams.get('curso')

    const ans = await fetch(`/stagely/backend/routes/adminRoutes.php?curso=${courseSelected}&action=fillInternship`)
    const infoPerCourse = await ans.json()
    console.log(infoPerCourse)

    const students = infoPerCourse.students || []
    const enterprises = infoPerCourse.enterprises || []

    // Filtra students e enterprises conforme o curso
    const studentsFiltered = courseSelected ? students.filter((s) => s.curso === courseSelected) : students

    const enterprisesFiltered = courseSelected ? enterprises.filter((e) => e.curso === courseSelected) : enterprises

    // Select de estudantes
    studentsList.innerHTML = ''
    students.forEach((s) => {
      const li = document.createElement('li')
      li.className = 'item item-student'
      li.dataset.value = s.id
      li.dataset.course = s.curso
      const thumbnail = document.createElement('div')
      thumbnail.className = 'student-thumbnail'
      li.appendChild(thumbnail)
      const span = document.createElement('span')
      span.textContent = s.nome
      li.appendChild(span)
      studentsList.appendChild(li)
    })

    // Select de empresas
    enterpriseList.innerHTML = ''
    enterprises.forEach((e) => {
      const li = document.createElement('li')
      li.className = 'item item-enterprise'
      li.dataset.value = e.id
      li.dataset.course = e.curso
      const thumbnail = document.createElement('div')
      thumbnail.className = 'enterprise-thumbnail'
      li.appendChild(thumbnail)
      const span = document.createElement('span')
      span.textContent = e.nome
      li.appendChild(span)
      enterpriseList.appendChild(li)
    })
  } catch (error) {
    console.error('Erro ao carregar filtros:', error)
  }
})
