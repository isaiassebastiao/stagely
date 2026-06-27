document.addEventListener('DOMContentLoaded', async () => {
  try {
    const studentsList = document.querySelector('#data-students .list')
    const enterpriseList = document.querySelector('#data-enterprise .list')

    const urlParams = new URLSearchParams(window.location.search)
    const enterpriseId = urlParams.get('id')
    const enterpriseArea = urlParams.get('area')
    const course = urlParams.get('curso')

    const ans = await fetch(`/stagely/backend/routes/adminRoutes.php?action=registerInfo`)
    const staticInfo = await ans.json()

    const ans1 = await fetch(`/stagely/backend/routes/adminRoutes.php?action=enterpriseInterners&id=${enterpriseId}&area=${enterpriseArea}&curso=${course}`)
    const result = await ans1.json()
    const students = result.data

    studentsList.innerHTML = ''
    students.forEach((s) => {
      const li = document.createElement('li')
      li.className = 'item item-student'
      li.dataset.value = s.id
      const thumbnail = document.createElement('div')
      thumbnail.className = 'student-thumbnail'
      li.appendChild(thumbnail)
      const span = document.createElement('span')
      span.textContent = s.nome

      li.appendChild(span)
      studentsList.appendChild(li)
    })

    if (window.initializeCustomSelects) {
      window.initializeCustomSelects()
    }
  } catch (error) {
    console.error('Erro ao carregar filtros:', error)
  }
})
