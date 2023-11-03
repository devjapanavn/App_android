import {createSlice} from '@reduxjs/toolkit';
const initialState = {
  androidVersion: 1,
  pathVideo: null,
  pathImage: null,
  hotline: null,
  bank: null,
  social: null,
  isShowIntro: true,
  id_page_home: 0,
  id_page_promotion: 0,
  id_static_ve_chung_toi: 0,
  first_image_array: [],
};

export const rootSlice = createSlice({
  name: 'root',
  initialState,
  reducers: {
    hideIntro: state => {
      return {
        ...state,
        isShowIntro: false,
      };
    },
    getVersion: (state, action) => {
      return {
        ...state,
        androidVersion: action.payload.android || 1,
        id_page_promotion: action.payload.id_page_promotion || 0,
        id_page_home: action.payload.id_page_home || 0,
        pathVideo: action.payload.path_video || null,
        pathImage: action.payload.path || null,
        hotline: action.payload.hotline || null,
        bank: action.payload.bank || null,
        social: action.payload.social || null,
        id_static_ve_chung_toi: action.payload.id_static_ve_chung_toi || null,
        first_image_array: action.payload.first_image_array || [],
      };
    },
  },
});
export const {getVersion, hideIntro} = rootSlice.actions;
export default rootSlice.reducer;
