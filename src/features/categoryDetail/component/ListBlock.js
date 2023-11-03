import {BannerCarousel, ImageBlock} from '@app/components';
import {BLOCK_ENUM} from '@app/constants';
import {onPressLink} from '@app/utils';
import _ from 'lodash';
import React from 'react';
import {View} from 'react-native';

const component = ({blocks}) => {

  if (blocks && blocks.length > 0) {
    let contents = [];
    blocks.forEach((item, index) => {
      switch (item.name_code) {
        case BLOCK_ENUM.BLOCK_CAROUSEL:
          contents.push(
            <BannerCarousel
              title={item.data_block ? item.data_block['tieu-de'] : null}
              type={item.data_block.nang_cao?.kieu_hien_thi}
              banners={item.data_block.banner}
              showDot={true}
              backgroundColor="#fff"
              showStype={item.data_block?.nang_cao?.mobile}
              onPress={onPressLink}
            />,
          );
          break;

        case BLOCK_ENUM.BLOCK_GALLERY:
          contents.push(
            <ImageBlock
              banners={item.data_block?.banner}
              onPressLink={onPressLink}
            />,
          );
          break;
      }
    });
    return <View>{contents}</View>;
  }
  return <View />;
};
export const ListBlocks = React.memo(
  component,
  (prev, next) => prev.blocks === next.blocks,
);
