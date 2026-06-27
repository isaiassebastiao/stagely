;(() => {
  const eyeIcons = document.querySelectorAll('.icon-eye')

  function togglePasswordVisibility(event) {
    const eyeIcon = event.currentTarget
    const field = eyeIcon.closest('.field')
    const passwordInput = field.querySelector('input[type="password"], input[type="text"]')

    if (!passwordInput) return

    const isPassword = passwordInput.type === 'password'
    passwordInput.type = isPassword ? 'text' : 'password'
    eyeIcon.classList.toggle('active', isPassword)
  }

  eyeIcons.forEach((icon) => {
    icon.addEventListener('click', togglePasswordVisibility)
  })
})()
