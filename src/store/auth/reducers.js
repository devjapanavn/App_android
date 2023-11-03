import { createSlice } from '@reduxjs/toolkit';
const initialState = {
  user: null,
  isLogin: false,
  totalCart: 0,
  totalCartPrice: 0,
  text_promotion_cart: null,
};

export const authSlice = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    loginSuccess: (state, action) => {
      return {
        ...state,
        user: action.payload,
        isLogin: true,
      };
    },
    updateUser: (state, action) => {
      return {
        ...state,
        user: action.payload,
      };
    },
    logoutSuccess: (state, action) => {
      return {
        ...state,
        user: null,
        isLogin: false,
        totalCart: 0,
        totalCartPrice: 0,
      };
    },
    setTotalCart: (state, action) => {
      return {
        ...state,
        totalCart: action.payload.total_item,
        totalCartPrice: action.payload.total,
        text_promotion_cart: action.payload.text_promotion_cart,
      };
    },
    clearTotalCart: (state, action) => {
      return {
        ...state,
        totalCart: 0,
      };
    },
  },
});
export const {
  loginSuccess,
  logoutSuccess,
  setTotalCart,
  clearTotalCart,
  updateUser,
} = authSlice.actions;
export default authSlice.reducer;
