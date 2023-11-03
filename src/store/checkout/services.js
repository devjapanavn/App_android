import {listOrderSaleAuto, setListVoucher} from './reducers';

function orderSaleAuto(data) {
  return dispatch => dispatch(listOrderSaleAuto(data));
}
function updatelistVoucher(data) {
  return dispatch => dispatch(setListVoucher(data));
}
export {orderSaleAuto,updatelistVoucher};
