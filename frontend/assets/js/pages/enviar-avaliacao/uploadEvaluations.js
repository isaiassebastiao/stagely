;(() => {
  const getLoaderDelay = (startTime, minimum = 500) => {
    const elapsed = Date.now() - startTime
    return elapsed < minimum ? minimum - elapsed : 0
  }

  const fileInput = document.getElementById('fileInput')
  const fileName = document.getElementById('fileName')

  fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
      const file = fileInput.files[0]

      if (file.type !== 'application/pdf') {
        showAlert('O ficheiro deve estar no formato PDF', 'error')
        fileInput.value = ''
        fileName.textContent = 'Apenas ficheiros no formato PDF são aceites'
        fileName.style.color = '#8f8888'
        return
      }

      fileName.textContent = file.name
      fileName.style.color = '#ffffff'
    }
  })

  async function uploadEvaluations(event) {
    event.preventDefault()

    const submitButton = event.submitter || event.target.querySelector('button[type="submit"], input[type="submit"]')
    await sendEvaluation(false, submitButton)
  }

  async function sendEvaluation(replace = false, submitButton = null) {
    const form = document.getElementById('form')
    if (!submitButton) {
      submitButton = form?.querySelector('button[type="submit"], input[type="submit"]')
    }
    const urlParams = new URLSearchParams(window.location.search)
    const internerId = urlParams.get('id')

    if (!fileInput.files.length) {
      showAlert('Selecione a ficha de avaliação em PDF', 'error')
      return
    }

    if (submitButton) {
      submitButton.disabled = true
      submitButton.classList.add('loading')
    }

    const startTime = Date.now()
    const formData = new FormData()
    formData.append('avaliacao', fileInput.files[0])

    try {
      const response = await fetch(`/stagely/backend/routes/enterpriseRoutes.php?action=saveEvaluation&id=${internerId}${replace ? '&replace=1' : ''}`, {
        method: 'POST',
        body: formData
      })

      const result = await response.json()
      const waitTime = getLoaderDelay(startTime)

      setTimeout(() => {
        if (submitButton) {
          submitButton.disabled = false
          submitButton.classList.remove('loading')
        }

        if (result.success) {
          showAlert(result.message, 'success')

          setTimeout(() => {
            window.location.href = '/stagely/frontend/pages/listar-estagiarios/index.php'
          }, 1000)
          return
        }

        if (result.found) {
          openConfirmModal({
            type: 'replace',
            title: 'Substituir avaliação',
            message: result.message || 'Uma avaliação já existe. Deseja substituí-la?',
            confirmText: 'Substituir',
            cancelText: 'Cancelar',
            onConfirm: () => sendEvaluation(true)
          })
          return
        }

        showAlert(result.message, 'error')
      }, waitTime)
    } catch (error) {
      console.error(error)
      const waitTime = getLoaderDelay(startTime)

      setTimeout(() => {
        if (submitButton) {
          submitButton.disabled = false
          submitButton.classList.remove('loading')
        }
        showAlert('Erro de conexão. Tente novamente.', 'error')
      }, waitTime)
    }
  }

  /* Reset ao carregar */
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form')
    const submitButton = form?.querySelector('button[type="submit"], input[type="submit"]')

    if (form && typeof form.reset === 'function') form.reset()

    if (submitButton) {
      submitButton.disabled = false
      submitButton.classList.remove('loading')
    }
  })

  // Evita cache e recarrega se a página voltar do histórico
  window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
      window.location.reload()
    }
  })
  window.uploadEvaluations = uploadEvaluations
})()
