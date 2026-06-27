document.addEventListener('DOMContentLoaded', async () => {
  try {
    var activityList = document.querySelector('#data-area-activity .list')

    const response = await fetch('/stagely/backend/routes/adminRoutes.php?action=registerInfo')
    const result = await response.json()

    result.actingArea.forEach((d) => {
      activityList.innerHTML += `<li class="item item-area" data-value="${d.id}" data-area="${d.nome}"><div class="area-thumbnail"></div>${d.nome}</li>`
    })
  } catch (error) {
    console.error('Erro ao carregar filtros:', error)
  }
})
