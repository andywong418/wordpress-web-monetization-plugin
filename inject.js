window.onload = function() {

  const paymentPointer = (php_vars && php_vars[0].payment_pointer) || ''
  const addCoilAdvert = (php_vars && php_vars[0].add_coil_advert) || false
  console.log('paymentPointer', paymentPointer)
  console.log('addCoilADvert', addCoilAdvert)
  window.WebMonetizationScripts.donate({
    paymentPointer,
    addCoilAdvert
  })
}
