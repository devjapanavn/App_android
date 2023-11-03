import {appDimensions, spacing} from '@app/assets';
import React, {useState, useEffect} from 'react';
import {
  View,
  StyleSheet,
  FlatList,
  useWindowDimensions,
  InteractionManager,
} from 'react-native';
import PagerView from 'react-native-pager-view';
import _ from 'lodash';
import {ProductItem} from '@app/components';
import {Dots} from './DotPagination';

const Component = ({products, indexBlock}) => {
  const [activeDot, setActiveDot] = useState(0);
  const [dataGroup, setDataGroup] = useState([]);
  const [onReady, setOnReady] = useState(false);
  const {width} = useWindowDimensions();
  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      interactionPromise.cancel();
    };
  }, []);

  useEffect(() => {
    if (products && products.length > 0) {
      setDataGroup(_.chunk(products, 4));
    }
  }, [products]);

  function onPageChange(e) {
    setActiveDot(e.nativeEvent.position);
  }

  if (!dataGroup || dataGroup.length === 0) {
    return <View />;
  }
  return (
    <>
      <PagerView
        style={styles.container}
        initialPage={0}
        onPageSelected={onPageChange}>
        {dataGroup &&
          dataGroup.length > 0 &&
          dataGroup.map((datas, index) => {
            if (onReady)
              return (
                <FlatList
                  listKey={(item, index) =>
                    `productslide_component__key_${indexBlock}_${item.id.toString()}`
                  }
                  key={`productSlider_${indexBlock}__${index}_2`}
                  keyExtractor={item =>
                    `productSlider_${indexBlock}__${index}_${item.id}`
                  }
                  style={styles.listContainer}
                  numColumns={2}
                  keyboardDismissMode="on-drag"
                  columnWrapperStyle={{
                    justifyContent: 'space-between',
                    marginBottom: spacing.small / 2,
                  }}
                  showsHorizontalScrollIndicator={false}
                  scrollEnabled={false}
                  data={datas}
                  renderItem={({item}) => (
                    <ProductItem
                      product={item}
                      containerStyle={{
                        width: width / 2 - 20,
                        height: 280,
                      }}
                    />
                  )}
                />
              );
            return <View />;
          })}
      </PagerView>
      <Dots
        length={dataGroup?.length}
        active={activeDot}
        activeDotWidth={28}
        activeColor={'#888888'}
        passiveColor={'#888888'}
      />
    </>
  );
};

function areEqual(prevProps, nextProps) {
  return _.isEqual(prevProps.products, nextProps.products);
}
export const ListProductSlide = React.memo(Component, areEqual);
const styles = StyleSheet.create({
  container: {
    height: 580,
    width: '100%',
    position: 'relative',
  },
  listContainer: {
   
  },
});
