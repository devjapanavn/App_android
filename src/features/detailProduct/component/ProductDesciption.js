import { appDimensions } from '@app/assets';
import { stringHelper } from '@app/utils';
import React, { useCallback, useState } from 'react';
import { FlatList, StyleSheet, View } from 'react-native';
import { Text, Divider, Button } from 'react-native-elements';
import RenderHtml from 'react-native-render-html';
const Component = ({ description }) => {
  const [textShown, setTextShown] = useState(false); //To show ur remaining Text
  const [lengthMore, setLengthMore] = useState(false); //to show the "Read more & Less Line"
  const toggleNumberOfLines = useCallback(() => {
    setTextShown(!textShown)
  }, [textShown]);

  const onLayout = useCallback(e => {
    setLengthMore(e.nativeEvent.layout.height >= 150); //to check the text is more than 4 lines or not
    // console.log(e.nativeEvent);
  }, []);

  return (
    <View style={styles.box}>
      <Text style={styles.headerTitleStyle}>Mô tả sản phẩm</Text>
      <View style={{ maxHeight: textShown ? null : 80, overflow: 'hidden' }}>
        <View onLayout={onLayout} >
          <RenderHtml source={{ html: description }}
            systemFonts={['SF Pro Display']} contentWidth={appDimensions.width - 20} />
        </View>
      </View>
      <Divider />
      {lengthMore ? (
        <Button
          type="clear"
          onPress={toggleNumberOfLines}
          title={!textShown ? 'Xem thêm' : 'Rút gọn'}
          titleStyle={{ fontSize: 12 }}
          iconRight={true}
          icon={{
            name: !textShown ? 'chevron-down-outline' : 'chevron-up-outline',
            type: 'ionicon',
            color: '#0f83ff',
            size: 12,
          }}
        />
      ) : null}
    </View>
  );
};

const styles = StyleSheet.create({
  box: {
    marginVertical: 4,
    padding: 10,
    backgroundColor: '#fff'
  },
  headerTitleStyle: {
    fontSize: 16,
    color: '#000',
    fontWeight: '500',
    flex: 1,
    marginBottom: 8,
  },
  description: {
    fontSize: 14,
    lineHeight: 22,
    flexWrap: 'wrap',
  },
});

function areEqual(prev, next) {
  return true;
}
export const ProductDesciption = React.memo(Component, areEqual);
