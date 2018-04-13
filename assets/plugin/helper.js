function awebookingPriceFormat( price, decimals, decimalSep, thousandSep, currency, currencyPos ) {
  price = number_format( price, decimals, decimalSep, thousandSep );

  if ( currency.length ) {
    var sep = '';
    switch ( currencyPos ) {
      case 'left_space':
      case 'right_space':
        sep = ' ';
        break;
    }

    if ( currencyPos == 'left' ) {
      price = currency + sep + price;
    } else if ( currencyPos == 'right' ) {
      price = price + sep + currency;
    }
  }

  return price;
}
