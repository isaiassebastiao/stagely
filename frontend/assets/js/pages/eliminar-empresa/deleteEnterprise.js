;(() => {
  async function deleteEnterprise(id) {
    const btn = document.querySelector(`.delete[data-id="${id}"]`)
    const span = btn?.querySelector('span')

    if (btn) {
      btn.disabled = true
      if (span) span.textContent = 'Eliminando...'
      btn.style.color = 'var(--red)'
    }

    try {
      const response = await fetch('/stagely/backend/routes/adminRoutes.php?action=deleteEnterprise', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })

      if (!response.ok) {
        throw new Error('Erro na requisição')
      }

      const result = await response.json()

      if (result.success) {
        showAlert(result.message, 'success')

        if (typeof window.onEnterpriseDeleted === 'function') {
          window.onEnterpriseDeleted(id)
        }
      } else {
        showAlert(result.message, 'error')
      }
    } catch (error) {
      console.error(error)
      showAlert('Erro de conexão. Tente novamente.', 'error')
    } finally {
      if (btn) {
        btn.disabled = false
        if (span) span.textContent = 'Excluir'
      }
    }
  }

  function confirmDeleteEnterprise(id) {
    openConfirmModal({
      type: 'delete',
      title: 'Eliminar empresa',
      message: 'Tem certeza que deseja eliminar esta empresa?',
      onConfirm: () => deleteEnterprise(id)
    })
  }

  window.confirmDeleteEnterprise = confirmDeleteEnterprise
})()
