;(() => {
  const screenTitle = document.querySelector('header .screen h3')
  const screenIcon = document.querySelector('header .screen svg use')

  if (!screenTitle || !screenIcon) return

  const path = window.location.pathname

  const pages = [
    {
      match: '/inicio',
      title: 'Dashboard',
      icon: '#home'
    },
    {
      match: '/listar-empresas',
      title: 'Empresas',
      icon: '#building'
    },
    {
      match: '/cadastrar-empresa',
      title: 'Empresas',
      icon: '#building'
    },
    {
      match: '/visualizar-empresa',
      title: 'Empresas',
      icon: '#building'
    },
    {
      match: '/editar-empresa',
      title: 'Empresas',
      icon: '#building'
    },
    {
      match: '/listar-estagios',
      title: 'Estágios',
      icon: '#calendar'
    },
    {
      match: '/cadastrar-estagio',
      title: 'Estágios',
      icon: '#calendar'
    },
    {
      match: '/visualizar-estagio',
      title: 'Estágios',
      icon: '#calendar'
    },
    {
      match: '/editar-estagio',
      title: 'Estágios',
      icon: '#calendar'
    },
    {
      match: '/listar-avaliacoes',
      title: 'Avaliações',
      icon: '#form'
    },
    {
      match: '/alterar-senha',
      title: 'Alterar senha',
      icon: '#lock'
    },
    {
      match: '/editar-perfil',
      title: 'Editar perfil',
      icon: '#pencil'
    },
    {
      match: '/listar-estagiarios',
      title: 'Estagiários',
      icon: '#student'
    }
  ]

  const currentPage = pages.find((page) => path.includes(page.match))

  if (currentPage) {
    screenTitle.textContent = currentPage.title
    screenIcon.setAttribute('href', currentPage.icon)
  }
})()
