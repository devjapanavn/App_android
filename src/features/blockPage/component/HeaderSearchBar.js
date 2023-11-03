import {colors, spacing} from '@app/assets';
import React, {useCallback} from 'react';
import {StyleSheet, View} from 'react-native';
import {Icon, Text} from 'react-native-elements';
import {TouchableWithoutFeedback} from 'react-native-gesture-handler';
import {navigateRoute} from '@app/route';
import {ROUTES} from '@app/constants';
const component = () => {
  const handleGoToSearch = useCallback(() => {
    navigateRoute(ROUTES.SEARCH);
  }, []);
  return (
    <View style={styles.container}>
      <TouchableWithoutFeedback onPress={handleGoToSearch}>
        <View style={styles.searchContainer}>
          <Icon name="search" type="ionicon" color={colors.black} />
          <Text style={styles.searchTitle}>Tìm kiếm sản phẩm</Text>
        </View>
      </TouchableWithoutFeedback>
    </View>
  );
};
export const HeaderSearchBar = React.memo(component, () => true);

const styles = StyleSheet.create({
  container: {
    backgroundColor: colors.primary,
    paddingBottom:10
  },
  searchContainer: {
    height: 44,
    backgroundColor: 'white',
    flexDirection: 'row',
    alignItems: 'center',
    marginHorizontal: spacing.medium,
    padding: spacing.small,
    borderRadius: 4,
  },
  searchTitle: {
    marginLeft: spacing.small,
    color: colors.gray,
  },
});
