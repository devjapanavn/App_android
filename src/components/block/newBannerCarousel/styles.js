import React from 'react';
import {spacing} from '@app/assets';
import {StyleSheet} from 'react-native';
export default StyleSheet.create({
  container: {
    justifyContent: 'center',
  },
  itemBanner: {
    width: 300,
    height: 300,
  },
  txtTitle: {
    margin: spacing.medium,
    color: '#000000',
    fontSize: 15,
  },
  pagingContainer: {
    paddingVertical: 6,
  },
  pagingContainerInside: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
  },
  pagingDotInsideStyle: {
    width: 35,
    height: 8,
    borderRadius: 5,
    backgroundColor: '#fff',
  },
  pagingDotStyle: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#888',
  },
  pagingDotInactiveStyle: {
    width: 8,
    height: 8,
    borderRadius: 4,
  },
});
