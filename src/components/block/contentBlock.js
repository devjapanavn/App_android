import {appDimensions} from '@app/assets';
import {stringHelper} from '@app/utils';
import React from 'react';
import {StyleSheet, View} from 'react-native';
import RenderHtml from 'react-native-render-html';

const component = ({showStype, data, idBlock}) => {
  const styleContainer = {
    backgroundColor: showStype?.color_background || undefined,
    marginTop: stringHelper.formatToNumber(showStype?.margin?.top) || undefined,
    marginLeft:
      stringHelper.formatToNumber(showStype?.margin?.left) || undefined,
    marginRight:
      stringHelper.formatToNumber(showStype?.margin?.right) || undefined,
    marginBottom:
      stringHelper.formatToNumber(showStype?.margin?.bottom) || undefined,

    paddingLeft:
      stringHelper.formatToNumber(showStype?.padding?.left) || undefined,
    paddingRight:
      stringHelper.formatToNumber(showStype?.padding?.right) || undefined,
    paddingBottom:
      stringHelper.formatToNumber(showStype?.padding?.bottom) || undefined,
    paddingTop:
      stringHelper.formatToNumber(showStype?.padding?.top) || undefined,
  };

  if (data && data.content) {
    return (
      <View style={styleContainer}>
        <RenderHtml
          renderersProps={{img: {enableExperimentalPercentWidth: true}}}
          source={{html: data.content}}
          systemFonts={['SF Pro Display']}
          contentWidth={appDimensions.width - 20}
        />
      </View>
    );
  }
  return <View />;
};
export const ContentBlock = React.memo(component, () => true);

const styles = StyleSheet.create({});
