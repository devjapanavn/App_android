import React, { useEffect, useState } from 'react';
import { InteractionManager, SectionList, View } from 'react-native';
import PropTypes from 'prop-types';
import {
  Button,
  CheckBox,
  Divider,
  Header,
  Icon,
  ListItem,
  Text,
} from 'react-native-elements';
import _ from 'lodash';
import { colors } from '@app/assets';
import { stringHelper } from '@app/utils';
import api from '@app/api';
import { useQuery } from 'react-query';
import styles from './styles';
import RangePrice from './RangePrice';
const sectionTemps = [
  {
    name: 'Giá',
    data: [],
    type: 'RANGE_PRICE',
    selectAll: false,
  },
  {
    name: 'Danh mục',
    data: [],
    type: 'CATEGORIES',
    selectAll: false,
  },
  {
    name: 'Thương hiệu',
    data: [],
    type: 'BRANDS',
    selectAll: false,
  },
];
const fetch = async (categoryId, brandId, textSearch = "") => {
  return await api.getFilter(categoryId, brandId, textSearch);
};

const FilterDrawerComponent = ({
  onClose,
  categoryId,
  brandId,
  onFilter,
  defaultFilter,
  textSearch
}) => {
  const [rangePrice, setRangePrice] = useState({ from: 0, to: 0 });
  const [onReady, setOnReady] = useState(false);
  const [sections, setSection] = useState([]);

  useEffect(() => {
    const interactionPromise = InteractionManager.runAfterInteractions(() =>
      setOnReady(true),
    );
    return () => {
      setOnReady(false);
      interactionPromise.cancel();
    };
  }, []);

  const { status, data, error, refetch, isLoading } = useQuery(
    ['getFilter', { categoryId, brandId, textSearch: textSearch ? textSearch : "" }],
    () => fetch(categoryId, brandId, textSearch),
    {
      cacheTime: 0,
      staleTime: 0,
    },
  );

  useEffect(() => {
    const section = sectionTemps;
    setSection([]);
    if (data) {
      if (data.max_min) {
        setRangePrice({ from: data.max_min.minPrice, to: data.max_min.maxPrice });
        section[0].data = [{ id: 0 }];
      }
      if (data.count_category) {
        section[1].data = _.map(data.count_category, item => {
          let selected = false;
          if (defaultFilter?.category) {
            selected = _.includes(defaultFilter.category, item.id);
          }
          return { ...item, selected: selected };
        });
      }
      if (data.count_brand) {
        section[2].data = _.map(data.count_brand, item => ({
          ...item,
          select:
            defaultFilter && defaultFilter.brand
              ? _.includes(defaultFilter.brand, item.id)
              : false,
        }));
      }
      setSection(section);
    }
  }, [data]);

  function toggleCheckBox(type) {
    const st = _.map(sections, section => {
      if (section.type === type) {
        section.selectAll = !section.selectAll;
        section.data = _.map(section.data, child => ({
          ...child,
          select: section.selectAll,
        }));
      }
      return section;
    });
    setSection(st);
  }

  function toggleCheckBoxChild(type, childId) {
    const st = _.map(sections, section => {
      if (section.type === type) {
        section.data = _.map(section.data, child => {
          if (childId === child.id) {
            child.select = !child.select;
          }
          return child;
        });
        const totalSelect = _.countBy(section.data, dt => dt.select === true);
        section.selectAll = totalSelect['true'] === section.data.length;
      }
      return section;
    });
    setSection(st);
  }

  function onSubmit() {
    let category_ids = [];
    let brand_check = [];
    const beginMinPrice = rangePrice.from;
    const endMaxPrice = rangePrice.to;
    if (sections[1].data && sections[1].data.length > 0) {
      category_ids = _.chain(sections[1].data)
        .filter(dt => dt.select)
        .map(dt => dt.id)
        .value();
    }
    if (sections[2].data && sections[2].data.length > 0) {
      brand_check = _.chain(sections[2].data)
        .filter(dt => dt.select)
        .map(dt => dt.id)
        .value();
    }
    if (onFilter) {
      onFilter({
        category_check: category_ids.join(','),
        brand_check: brand_check.join(','),
        beginMinPrice,
        endMaxPrice,
      });
      onClose();
    }
  }

  const renderItem = ({ section, item }) => {
    if (section.type === 'RANGE_PRICE') return <View />;
    return (
      <ListItem
        bottomDivider
        onPress={() => toggleCheckBoxChild(section.type, item.id)}>
        <ListItem.Content>
          <ListItem.Subtitle style={{ fontSize: 15 }}>
            {item.name_vi}
          </ListItem.Subtitle>
        </ListItem.Content>
        <ListItem.CheckBox
          checked={item.select}
          uncheckedColor={'transparent'}
          checkedIcon={
            <Icon
              name="checkmark-sharp"
              type="ionicon"
              color="rgb(0, 153, 255)"
            />
          }
        />
      </ListItem>
    );
  };

  const renderSectionHeader = ({ section }) => {
    switch (section.type) {
      case 'RANGE_PRICE':
        return (
          <RangePrice
            min={data?.max_min?.minPrice}
            max={data?.max_min?.maxPrice}
            onChangeValue={val => setRangePrice(val)}
          />
        );
      default:
        if (section.data.length > 0) {
          return (
            <View style={styles.sectionHeader}>
              <Text style={styles.sectionHeaderTitle}>{section.name}</Text>
              <CheckBox
                title="Chọn tất cả"
                titleProps={{
                  style: styles.sectionCheckboxTitle,
                }}
                checked={section.selectAll}
                onPress={() => toggleCheckBox(section.type)}
                containerStyle={styles.sectionCheckboxContainer}
              />
            </View>
          );
        }

        return null;
    }
  };

  return (
    <View style={{ backgroundColor: '#fff', flex: 1 }}>
      {onReady && !isLoading ? (
        <>
          <Header
            backgroundColor={colors.primary}
            rightComponent={{
              icon: 'md-close-circle-outline',
              type: 'ionicon',
              color: '#fff',
              size: 30,
              onPress: () => onClose(),
            }}
            leftComponent={{
              text: 'Bộ lọc',
              style: { color: '#fff', fontSize: 24, fontWeight: '500' },
            }}
            elevated
          />
          <SectionList
            removeClippedSubviews={true}
            keyExtractor={(item, index) => `filterItem_${item.id}`}
            sections={sections}
            renderItem={renderItem}
            renderSectionHeader={renderSectionHeader}
          />
          <View style={styles.footerContainer}>
            <Divider />
            <Button
              onPress={() => onClose()}
              title={'Đóng'}
              type="outline"
              containerStyle={styles.footerButtonContainer}
              buttonStyle={{ borderColor: '#3b4859' }}
              titleStyle={{ color: '#3b4859' }}
            />
            <Button
              title={'Áp dụng'}
              type="solid"
              onPress={() => onSubmit()}
              containerStyle={styles.footerButtonContainer}
              buttonStyle={{ backgroundColor: '#dc0000' }}
            />
          </View>
        </>
      ) : null}
    </View>
  );
};

FilterDrawerComponent.propTypes = {
  onClose: PropTypes.func.isRequired,
  categoryId: PropTypes.number,
  brandId: PropTypes.number,
  type: PropTypes.string,
  onFilter: PropTypes.func.isRequired,
};

FilterDrawerComponent.defaultProps = {
  type: 'province',
  categoryId: 0,
  brandId: 0
};
export const FilterDrawer = React.memo(
  FilterDrawerComponent,
  (prev, next) =>
    prev.categoryId === next.categoryId &&
    _.isEqual(prev.onFilter, next.onFilter),
);
