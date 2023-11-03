import React, { useEffect, useRef, useState } from 'react';
import {
  View,
  TextInput,
  TouchableWithoutFeedback,
  Keyboard,
  Platform,
  I18nManager,
  Text,
  ViewPropTypes,
  StyleSheet,
} from 'react-native';
import Clipboard from '@react-native-clipboard/clipboard';
import Proptypes from 'prop-types';
import { stringHelper } from '@app/utils';
const majorVersionIOS = parseInt(String(Platform.Version), 10);
const isAutoFillSupported = Platform.OS === 'ios' && majorVersionIOS >= 12;

const Component = props => {
  const fields = useRef([]);
  const [digits, setDigits] = useState(stringHelper.codeToArray(props.code));
  const [selectedIndex, setSelectedIndex] = useState(
    props.autoFocusOnLoad ? 0 : -1,
  );
  let timer = null;
  let hasCheckedClipBoard = false;
  useEffect(() => {
    copyCodeFromClipBoardOnAndroid();
    bringUpKeyBoardIfNeeded();
    const keyboardDidHideListener = Keyboard.addListener(
      'keyboardDidHide',
      handleKeyboardDidHide,
    );
    return () => {
      keyboardDidHideListener.remove();
    };
  }, []);

  useEffect(() => {
    setSelectedIndex(stringHelper.codeToArray(props.code));
  }, [props.code]);

  const handleKeyboardDidHide = () => {
    blurAllFields();
  };

  const bringUpKeyBoardIfNeeded = () => {
    const { autoFocusOnLoad, pinCount } = props;
    const digits = getDigits();
    const focusIndex = digits.length ? digits.length - 1 : 0;
    if (focusIndex < pinCount && autoFocusOnLoad) {
      focusField(focusIndex);
    }
  };

  function copyCodeFromClipBoardOnAndroid() {
    if (Platform.OS === 'android') {
      checkPinCodeFromClipBoard();
      timer = setInterval(checkPinCodeFromClipBoard, 400);
    }
  }

  const notifyCodeChanged = () => {
    const code = digits.join('');
    const { onCodeChanged } = props;
    if (onCodeChanged) {
      onCodeChanged(code);
    }
  };

  const checkPinCodeFromClipBoard = () => {
    const { pinCount, onCodeFilled } = props;
    const regexp = new RegExp(`^\\d{${pinCount}}$`);
    Clipboard.getString()
      .then(code => {
        if (
          hasCheckedClipBoard &&
          regexp.test(code) &&
          clipBoardCode !== code
        ) {
          setDigits(code.split(''));
          blurAllFields();
          notifyCodeChanged();
          onCodeFilled && onCodeFilled(code);
        }
        clipBoardCode = code;
        hasCheckedClipBoard = true;
      })
      .catch(() => { });
  };

  const getDigits = () => {
    const { code } = props;
    return code === undefined ? digits : code.split('');
  };

  function handleChangeText(index, text) {
    const { onCodeFilled, pinCount } = props;
    const digits = getDigits();
    let newdigits = digits.slice();
    const oldTextLength = newdigits[index] ? newdigits[index].length : 0;
    const newTextLength = text.length;
    if (newTextLength - oldTextLength === pinCount) {
      // user pasted text in.
      newdigits = text.split('').slice(oldTextLength, newTextLength);
      setDigits(newdigits);
      notifyCodeChanged();
    } else {
      if (text.length === 0) {
        if (newdigits.length > 0) {
          newdigits = newdigits.slice(0, newdigits.length - 1);
        }
      } else {
        text.split('').forEach(value => {
          if (index < pinCount) {
            newdigits[index] = value;
            index += 1;
          }
        });
        index -= 1;
      }

      setDigits(newdigits);
      notifyCodeChanged();
    }

    let result = newdigits.join('');
    if (result.length >= pinCount) {
      onCodeFilled && onCodeFilled(result);
      focusField(pinCount - 1);
      blurAllFields();
    } else {
      if (text.length > 0 && index < pinCount - 1) {
        focusField(index + 1);
      }
    }
  }

  function handleKeyPressTextInput(index, key) {
    const digits = getDigits();
    if (key === 'Backspace') {
      if (!digits[index] && index > 0) {
        handleChangeText(index - 1, '');
        focusField(index - 1);
      }
    }
  }

  const focusField = index => {
    if (index < fields.current.length) {
      fields.current[index].focus();
      setSelectedIndex(index);
    }
  };

  const blurAllFields = () => {
    fields.current.forEach(field => field.blur());
    setSelectedIndex(-1);
  };

  const clearAllFields = () => {
    const { clearInputs, code } = props;
    if (clearInputs && code === '') {
      setDigits([]);
      setSelectedIndex(0);
    }
  };

  const renderOneInputField = (_, index) => {
    const { defaultTextFieldStyle } = styles;
    const {
      codeInputFieldStyle,
      codeInputHighlightStyle,
      secureTextEntry,
      editable,
      keyboardType,
      selectionColor,
      keyboardAppearance,
      clearInputs,
      placeholderCharacter,
      placeholderTextColor,
    } = props;
    const { color: defaultPlaceholderTextColor } = {
      ...defaultTextFieldStyle,
      ...codeInputFieldStyle,
    };
    return (
      <View pointerEvents="none" key={index + 'view'} testID="inputSlotView">
        <TextInput
          testID="textInput"
          underlineColorAndroid="rgba(0,0,0,0)"
          style={
            selectedIndex === index
              ? [
                defaultTextFieldStyle,
                codeInputFieldStyle,
                codeInputHighlightStyle,
              ]
              : [defaultTextFieldStyle, codeInputFieldStyle]
          }
          ref={ref => {
            fields.current[index] = ref;
          }}
          onChangeText={text => {
            handleChangeText(index, text);
          }}
          onKeyPress={({ nativeEvent: { key } }) => {
            handleKeyPressTextInput(index, key);
          }}
          value={!clearInputs ? digits[index] : ''}
          keyboardAppearance={keyboardAppearance}
          keyboardType={keyboardType}
          textContentType={isAutoFillSupported ? 'oneTimeCode' : 'none'}
          key={index}
          selectionColor={selectionColor}
          secureTextEntry={secureTextEntry}
          editable={editable}
          placeholder={placeholderCharacter}
          placeholderTextColor={
            placeholderTextColor || defaultPlaceholderTextColor
          }
        />
      </View>
    );
  };

  const renderTextFields = () => {
    const { pinCount } = props;
    const array = new Array(pinCount).fill(0);
    return array.map(renderOneInputField);
  };

  return (
    <View testID="OTPInputView" style={props.style}>
      <TouchableWithoutFeedback
        style={{ width: '100%', height: '100%' }}
        onPress={() => {
          if (!props.clearInputs) {
            let filledPinCount = digits.filter(digit => {
              return digit !== null && digit !== undefined;
            }).length;
            focusField(Math.min(filledPinCount, props.pinCount - 1));
          } else {
            clearAllFields();
            focusField(0);
          }
        }}>
        <View
          style={{
            flexDirection: I18nManager.isRTL ? 'row-reverse' : 'row',
            justifyContent: 'space-between',
            alignItems: 'center',
            width: '100%',
            height: '100%',
          }}>
          {renderTextFields()}
        </View>
      </TouchableWithoutFeedback>
    </View>
  );
};

Component.propTypes = {
  pinCount: Proptypes.number,
  codeInputFieldStyle: Text.propTypes.style,
  codeInputHighlightStyle: Text.propTypes.style,
  onCodeFilled: Proptypes.func,
  onCodeChanged: Proptypes.func,
  autoFocusOnLoad: Proptypes.bool,
  code: Proptypes.string,
  secureTextEntry: Proptypes.bool,
  editable: Proptypes.bool,
  keyboardType: Proptypes.oneOf([
    'default',
    'email-address',
    'number-pad',
    'phone-pad',
  ]),
  placeholderCharacter: Proptypes.string,
  placeholderTextColor: Proptypes.string,
  style: ViewPropTypes.style,
  selectionColor: Proptypes.string,
  clearInputs: Proptypes.bool,
  keyboardAppearance: Proptypes.oneOf(['default', 'dark', 'light']),
};

Component.defaultProps = {
  pinCount: 6,
  autoFocusOnLoad: true,
  secureTextEntry: false,
  editable: true,
  keyboardAppearance: 'default',
  keyboardType: 'number-pad',
  clearInputs: false,
  placeholderCharacter: '',
  selectionColor: '#000',
};

function areEqual(prev, next) {
  return prev.pinCount === next.pinCount && prev.code === next.code;
}
export const OtpInput = React.memo(Component, areEqual);
const styles = StyleSheet.create({
  defaultTextFieldStyle: {
    width: 45,
    height: 45,
    borderColor: 'rgba(226, 226, 226, 1)',
    borderWidth: 1,
    borderRadius: 2,
    textAlign: 'center',
    color: 'black',
  },
});
