;(() => {
  const eyeIcon = document.querySelector('.icon-eye')
  const passwordInput = document.querySelector('#password')

  function togglePasswordVisibility() {
    const isPassword = passwordInput.type === 'password'
    passwordInput.type = isPassword ? 'text' : 'password'
    eyeIcon.classList.toggle('active', isPassword)
  }

  eyeIcon.addEventListener('click', togglePasswordVisibility)
})()
