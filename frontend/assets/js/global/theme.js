;(() => {
  const THEME_KEY = 'stagely-theme'
  const DARK_THEME = 'dark'
  const LIGHT_THEME = 'light'

  // Detectar tema do sistema
  function getSystemTheme() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      return DARK_THEME
    }
    return LIGHT_THEME
  }

  // Inicializa o tema ao carregar a página
  function initTheme() {
    const savedTheme = localStorage.getItem(THEME_KEY)
    const theme = savedTheme || getSystemTheme()
    applyTheme(theme, false)
  }

  // Aplica o tema ao documento
  function applyTheme(theme, savePreference = true) {
    if (theme === LIGHT_THEME) {
      document.documentElement.setAttribute('data-theme', LIGHT_THEME)
    } else {
      document.documentElement.removeAttribute('data-theme')
    }

    updateThemeButton(theme)
    updateLogo(theme)

    if (savePreference) {
      localStorage.setItem(THEME_KEY, theme)
    }
  }

  // Atualiza o texto e ícone do botão de tema
  function updateThemeButton(currentTheme) {
    const themeButton = document.getElementById('themeToggle')
    if (!themeButton) return

    const svg = themeButton.querySelector('svg')
    const span = themeButton.querySelector('span')

    if (currentTheme === LIGHT_THEME) {
      svg.innerHTML = '<use href="#sun"></use>'
      span.textContent = 'Modo claro'
    } else {
      svg.innerHTML = '<use href="#moon"></use>'
      span.textContent = 'Modo escuro'
    }
  }

  // Atualiza a logo baseado no tema
  function updateLogo(theme) {
    const logoSelectors = ['aside .logo img', '.login .logo', 'header .screen menu .menu-logo img']

    const newSrc = theme === LIGHT_THEME ? '/stagely/frontend/assets/images/logo-2.svg' : '/stagely/frontend/assets/images/logo.svg'

    logoSelectors.forEach((selector) => {
      document.querySelectorAll(selector).forEach((logo) => {
        logo.src = newSrc
      })
    })
  }

  // Toggle do tema
  function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') === LIGHT_THEME ? LIGHT_THEME : DARK_THEME
    const newTheme = currentTheme === LIGHT_THEME ? DARK_THEME : LIGHT_THEME
    applyTheme(newTheme, true)
  }

  // Event listener para o botão de tema
  function setupThemeToggle() {
    const themeButton = document.getElementById('themeToggle')
    if (themeButton) {
      themeButton.addEventListener('click', (e) => {
        e.preventDefault()
        toggleTheme()
      })
    }
  }

  // Monitorar mudanças no tema do sistema
  function setupSystemThemeListener() {
    if (window.matchMedia) {
      const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)')
      darkModeQuery.addEventListener('change', (e) => {
        const savedTheme = localStorage.getItem(THEME_KEY)
        // Só muda para tema do sistema se não havia preferência salva
        if (!savedTheme) {
          const newTheme = e.matches ? DARK_THEME : LIGHT_THEME
          applyTheme(newTheme, false)
        }
      })
    }
  }

  // Inicializa quando o DOM estiver pronto
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initTheme()
      setupThemeToggle()
      setupSystemThemeListener()
    })
  } else {
    initTheme()
    setupThemeToggle()
    setupSystemThemeListener()
  }
})()
