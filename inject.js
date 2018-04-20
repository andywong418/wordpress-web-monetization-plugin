window.onload = function() {
  // Inject window.monetize into your own payment pointer.
  if(window.monetize) {
    var receiver = (php_vars && php_vars[0].payment_pointer) || ''
    return window.monetize({ receiver })
  } else {
    // Show error box to say window.monetize is not defined and to turn it on in admin page.
    alert('Your extension is not turned on or downloaded. Please download a web monetization extention.')
    return new Error('window.monetize is not defined!')
  }
}
