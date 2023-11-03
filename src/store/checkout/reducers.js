import { createSlice } from '@reduxjs/toolkit';
const initialState = {
  gift_order_auto: "",
  voucher_code: ""
};

export const checkoutSlice = createSlice({
  name: 'checkout',
  initialState,
  reducers: {
    listOrderSaleAuto: (state, action) => {
      console.log('------------action.payload checkout sale auto----------')
      console.log(JSON.parse(action.payload.gift_order_auto))
      return {
        ...state,
        gift_order_auto: action.payload.gift_order_auto,
        voucher_code: action.payload.voucher_code,
      };
    },
    setListVoucher: (state, action) => {
      return {
        ...state,
        voucher_code: action.payload.voucher_code,
      }
    },
  },
});
export const {
  listOrderSaleAuto,setListVoucher
} = checkoutSlice.actions;
export default checkoutSlice.reducer;