import { initDialogs } from "../../resources/js/dialog.js"

// Dialogs: a .dialog-trigger opens the <dialog> its data-dialog-target selects
initDialogs()

;(function () {
  // Copy buttons: a [data-copy] button copies its value to the clipboard and confirms it
  var buttons = document.querySelectorAll("[data-copy]")

  Array.prototype.forEach.call(buttons, function (button) {
    var label = button.textContent

    button.addEventListener("click", function () {
      if (!navigator.clipboard) {
        return
      }
      navigator.clipboard
        .writeText(button.getAttribute("data-copy"))
        .then(function () {
          button.textContent = "Copied"
          setTimeout(function () {
            button.textContent = label
          }, 1500)
        })
    })
  })
})()
