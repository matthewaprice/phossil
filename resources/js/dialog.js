function initDialogs() {
  // Handle dialog triggers
  document.querySelectorAll(".dialog-trigger").forEach((trigger) => {
    trigger.addEventListener("click", function (e) {
      e.preventDefault()
      const dialogId = this.dataset.dialogTarget
      const dialog = document.querySelector(dialogId)

      if (dialog) {
        openDialog(dialog)
      }
    })
  })
}

function openDialog(dialog) {
  dialog.showModal()

  // Focus on specified field if data attribute exists
  const fieldFocus = dialog.dataset.fieldFocus
  if (fieldFocus) {
    const field = dialog.querySelector(`#${fieldFocus}`)
    if (field) {
      setTimeout(() => field.focus(), 100)
    }
  } else {
    // Remove focus from any focused element (like the close button)
    setTimeout(() => {
      if (document.activeElement) {
        document.activeElement.blur()
      }
    }, 0)
  }

  // Setup close handlers
  setupDialogClose(dialog)
}

function setupDialogClose(dialog) {
  // Close button click - remove old listener first to prevent duplicates
  const closeBtn = dialog.querySelector(".dialog-close button")
  if (closeBtn) {
    const newCloseBtn = closeBtn.cloneNode(true)
    closeBtn.parentNode.replaceChild(newCloseBtn, closeBtn)
    newCloseBtn.addEventListener("click", function () {
      dialog.close()
    })
  }

  // Escape key
  dialog.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      dialog.close()
    }
  })

  // Click outside (on backdrop)
  dialog.addEventListener("click", function (e) {
    const dialogContent = dialog.querySelector(".dialog-panel")
    if (
      e.target === dialog ||
      (dialogContent && !dialogContent.contains(e.target))
    ) {
      dialog.close()
    }
  })
}

export { initDialogs, openDialog, setupDialogClose }
