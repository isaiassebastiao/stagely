;(function () {
  const input = document.getElementById('photoInput')
  const preview = document.getElementById('photoPreview')
  const container = document.querySelector('.upload .photo')

  if (!input || !preview || !container) return

  input.addEventListener('change', () => {
    const file = input.files[0]

    if (!file || !file.type.startsWith('image/')) return

    const reader = new FileReader()
    reader.onload = (e) => {
      preview.src = e.target.result
      preview.style.display = 'block'
      container.style.backgroundImage = 'none'
    }

    reader.readAsDataURL(file)
  })
})()
