import { colors, images, spacing } from '@app/assets';
import { ROUTES } from '@app/constants';
import { gobackRoute, navigateRoute } from '@app/route';
import _ from 'lodash';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import {
  Image,
  StatusBar,
  StyleSheet,
  TouchableOpacity,
  View,
} from 'react-native';
import { Badge, Button, Icon } from 'react-native-elements';
import { Searchbar } from 'react-native-paper';

const component = ({ onSearch }) => {
  useEffect(() => {
    StatusBar.setBackgroundColor(colors.primary);
    StatusBar.setBarStyle('light-content');
  }, []);
  const searchInp = useRef(null);
  const [search, setSearch] = useState(null);
  const handleDebounceSearch = useCallback(
    _.debounce(value => onSearch(value), 400),
    [],
  );
  useEffect(() => {
    searchInp.current.focus();
  }, []);

  useEffect(() => {
    handleDebounceSearch(search);
  }, [search]);

  const onBack = useCallback(() => {
    gobackRoute();
  }, []);

  const handDeleteKeyword = useCallback(() => {
    setSearch('');
    onSearch('');
  }, []);

  return (
    <View style={styles.container}>
      <View style={styles.headerContainer}>
        <Icon
          name="arrow-back-outline"
          type="ionicon"
          color={colors.white}
          size={25}
          onPress={onBack}
          activeOpacity={0.9}
          containerStyle={styles.headerAction}
        />
        <Searchbar
          ref={searchInp}
          placeholder="Tìm kiếm"
          style={{ flex: 1, height: 40, alignItems: 'center', paddingVertical: 0 }}
          autoCorrect={false}
          autoFocus={true}
          autoCapitalize="none"
          value={search}
          onChangeText={text => setSearch(text)}
        />
        <Button
          title="Hủy"
          type="clear"
          titleStyle={{ color: '#fff' }}
          onPress={() => gobackRoute()}
        />
      </View>
    </View>
  );
};
export const HeaderSearch = React.memo(component, () => true);

const styles = StyleSheet.create({
  container: {
    backgroundColor: colors.primary,
  },
  headerContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  headerImage: {
    height: 40,
    width: 120,
  },
  headerAction: {
    padding: spacing.medium,
  },
  headerBadgeCart: {
    position: 'absolute',
    top: 0,
    right: 0,
  },
});
