document.addEventListener('DOMContentLoaded', () => {
  const backBtn = document.querySelector('.buttons .btn.primary')

  if (backBtn) {
    const params = new URLSearchParams(window.location.search)
    const curso = params.get('curso') || 'ti'
    const dropdown = params.get('dropdown') || 'internships'

    backBtn.href = `/stagely/frontend/pages/listar-estagios/index.php?curso=${curso}&dropdown=${dropdown}`
  }
})
