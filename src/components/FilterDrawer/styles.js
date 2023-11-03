
import {StyleSheet} from 'react-native';
import _ from 'lodash';
import {globalStyles} from '@app/assets';
const THUMB_RADIUS = 8;
export default StyleSheet.create({
    modalFullsize: {
      margin: 0,
      padding: 0,
    },
  
    headerContainer: {
      flexDirection: '',
    },
    body: {},
    item: {
      color: '#2a2a2a',
      fontSize: 14,
      padding: 10,
    },
    resContainer: {
      flexDirection: 'row',
      alignItems: 'center',
      paddingVertical: 10,
    },
    selectedRes: {
      backgroundColor: '#e5f2ff',
      borderRadius: 4,
    },
    resCheckbox: {
      padding: 0,
      margin: 0,
    },
    resRightTitle: {
      fontSize: 14,
      color: '#000',
    },
    thumb: {
      width: THUMB_RADIUS * 2,
      height: THUMB_RADIUS * 2,
      borderRadius: THUMB_RADIUS,
      borderWidth: 2,
      borderColor: '#7f7f7f',
      backgroundColor: '#ffffff',
    },
    rail: {
      flex: 1,
      height: 3,
      borderRadius: 2,
      backgroundColor: '#c8c7cc',
    },
    railSelected: {
      height: 3,
      backgroundColor: '#dc0000',
      borderRadius: 2,
    },
    footerContainer: {
      flexDirection: 'row',
    },
    footerButtonContainer: {
      flex: 1,
      margin: 10,
    },
    sectionHeader: {
      flexDirection: 'row',
      paddingHorizontal: 10,
      alignItems: 'center',
      justifyContent: 'space-between',
      paddingVertical: 10,
    },
    sectionHeaderTitle: {
      ...globalStyles.text,
      fontSize: 17,
      fontWeight: '500',
    },
    sectionCheckboxContainer: {
      alignItems: 'center',
      padding: 0,
      margin: 0,
      backgroundColor: '#fff',
      borderColor: '#fff',
    },
    sectionCheckboxTitle: {
      fontSize: 13,
      fontWeight: 'normal',
      color: '#3b4859',
    },
  });
  