import {appDimensions, globalStyles, images} from '@app/assets';
import {BodyComponent, ImageReponsive} from '@app/components';
import {useRoute} from '@react-navigation/native';
import React from 'react';
import {StyleSheet, View} from 'react-native';
import {Text} from 'react-native-elements';
import {
  Appendix,
  AuthorInfomation,
  Comments,
  Header,
  ProductCountdown,
  ProductHorizon,
  RelatedNews,
  SectionNews,
  SuggestionProducts,
  Tag,
  TopCategories,
} from './component';

const Screen = props => {
  const route = useRoute();
  return (
    <BodyComponent style={styles.box}>
      <TopCategories />
      <ImageReponsive
        source={images.news_detail_banner_1}
        containerStyle={styles.banner}
        resizeMode="contain"
      />
      <ImageReponsive
        source={images.news_detail_banner_2}
        containerStyle={styles.banner}
        resizeMode="contain"
      />

      <View style={styles.container}>
        <Header />
        <Appendix />
        <ImageReponsive
          source={images.news_detail_content}
          containerStyle={styles.img_image}
          resizeMode="contain"
        />
        <Text style={styles.text_news}>
          Nhiều bạn vẫn cho rằng chỉ cần sử dụng kem chống nắng khi da trực tiếp
          tiếp xúc với ánh nắng mặt trời, chẳng hạn như tắm biển. Đó là một nhận
          định sai lầm khi tia nắng mặt trời sẽ ảnh hưởng xấu đến da bạn như gây
          ung thư da và kích ứng nặng nề. Tia UV là kẻ thù thầm lặng khi tiếp
          xúc với da, nhưng không phải ai cũng nhận thức được hậu quả. Hãy cùng
          Watsons tìm hiểu về thông tin của kem chống nắng để thay đổi suy nghĩ
          nhé!
        </Text>
        <Text style={styles.title_news}>
          1 Tại sao cần bảo vệ làn da của bạn khỏi ánh nắng mặt trời?
        </Text>
        <Text style={styles.text_news}>
          Tia UVA và UVB là một phần của quang phổ điện từ (ánh sáng) và cả hai
          đều có thể gây ung thư và tổn thương da lâu dài. UVA là tia UV có bước
          sóng dài hơn gây lão hóa da, trong khi UVB là tia UV có bước sóng ngắn
          hơn gây cháy nắng, bỏng da và làm đen da. Và việc sử dụng kem chống
          nắng là điều vô cùng quan trọng để bảo vệ da khỏi sự tấn công này.
        </Text>
        <ProductHorizon />

        <Text style={styles.title_news}>
          2 Khác biệt giữa kem chống nắng vật lý và kem chống nắng hóa học là
          gì?
        </Text>
        <Text style={styles.text_news}>
          _ Kem chống nắng vật lý: + Tác dụng ngay sau khi thoa lên da mà không
          cần đợi một khoảng thời gian + Ít gây kích ứng và phù hợp hơn cho da
          nhạy cảm + Tạo thành lớp chống nắng bền vững trong thời gian dài +
          Dưỡng ẩm nhiều hơn, chất kem dày, đặc nên dễ gây bí da, bít tắc lỗ
          chân lông dẫn tới mụn, da đổ dầu gây tối và sạm màu da. + Khó tiệp màu
          với lớp nền trang điểm.
        </Text>
        <ProductCountdown showCountDown={true} />
        <Text style={styles.text_news}>
          _ Kem chống nắng hóa học: + Kem chống nắng hóa học có kết cấu mỏng,
          nhẹ, ít nhờn rít, ít gây bít tắc lỗ chân lông + Không để lại vệt trắng
          bệt trên da, dễ thấm vào da và không làm da bị bóng dầu + Dễ tiệp màu
          da và cũng có thể sử dụng để thay để kem lót trang điểm.
        </Text>
        <ProductCountdown />
        <Text style={styles.title_news}>
          Một số tips để bảo vệ da và mắt của bạn khỏi tác hại của ánh nắng mặt
          trời
        </Text>
        <ImageReponsive
          source={images.news_detail_content}
          containerStyle={styles.img_image}
          resizeMode="contain"
        />
        <Text style={styles.text_news}>
          Đội mũ rộng vành và đeo kính râm có thể làm giảm tia UV đến mắt.Thoa
          kem chống nắng có chỉ số SPF 30 hoặc cao hơn sau mỗi 2 giờ để giữ an
          toàn dưới ánh nắng mặt trờiTránh ở ngoài trời nắng trực tiếp quá lâu,
          đặc biệt là những giờ tia UV mạnh nhất.Tìm bóng râm để hạn chế tiếp
          xúc với tia UV
        </Text>
        <Tag />
        <RelatedNews />
      </View>
      <Comments />
      <SectionNews type={'vertical'} />
      {/* <SuggestionProducts /> */}
      <AuthorInfomation />
      <SectionNews />
    </BodyComponent>
  );
};

const styles = StyleSheet.create({
  box: {
    backgroundColor: '#f1f1f1',
    flex: 1,
  },
  banner: {
    width: appDimensions.width - 10,
    margin: 5,
    backgroundColor: '#fff',
  },

  container: {
    paddingHorizontal: 10,
    backgroundColor: '#fff',
  },
  img_image: {
    width: appDimensions.width - 20,
    backgroundColor: '#fff',
    marginVertical: 5,
  },
  text_news: {
    ...globalStyles.text,
    lineHeight: 28,
    fontSize: 14,
    marginVertical: 5,
  },
  title_news: {
    ...globalStyles.text,
    lineHeight: 28,
    fontWeight: '500',
    fontSize: 20,
    marginVertical: 5,
  },
});

export const ListNewsScreen = Screen;
