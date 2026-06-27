document.addEventListener('DOMContentLoaded', () => {

  const novoBtn = document.querySelector('.buttons .btn[href*="cadastrar-estagio"]')

  if (novoBtn) {
    // Pega o curso da URL
    const params = new URLSearchParams(window.location.search)
    const curso = params.get('curso') || 'ti'

    novoBtn.href = `/stagely/frontend/pages/cadastrar-estagio/index.php?curso=${curso}&dropdown=internships`
  }
})
