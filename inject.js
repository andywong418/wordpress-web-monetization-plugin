window.onload = function() {

  const paymentPointer = (php_vars && php_vars[0].payment_pointer) || ''
  const addCoilAdvert = (php_vars && php_vars[1].add_coil_advert === '1') || false
  window.WebMonetizationScripts.donate({
    paymentPointer,
    addCoilAdvert
  })
}
