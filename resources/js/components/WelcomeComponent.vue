<template>
    <div class="u-margin__minus">
      <!-- hero -->
      <section class="p-hero">
        <img src="/images/hero.png" alt="" class="p-hero__image">
        <img src="/images/sp_hero2.png" alt="" class="p-hero__image hero-sp">
      </section>

      <!-- こんなお悩みありませんか？的な部分 -->
      <section class="p-catch">
        <div class="p-catch__container">
          <img src="images/top_catch01.png" alt="" class="p-catch__image">
          <img src="images/top_catch_sp02.png" alt="" class="p-catch__image catch-sp">
        </div>
      </section>

      <!-- 説明的な部分 -->
      <section class="p-about c-section">
        <h2 class="p-about__title">アイデアの「欲しい」をやり取り</h2>
        <strong class="p-about__title-sub">ABOUT</strong>

        <div class="p-about__container">
          
          <!-- card -->
          <div class="c-about">
          
            <div class="c-about__image">
              <img src="images/top_image01.png" alt="" class="c-about__image-item">
              <div class="c-about__image-shadow"></div>
            </div>

            <div class="c-about__contents">
              <strong class="c-about__contents-title">アイデアの投稿</strong>
              <p class="c-about__contents-text">スキルは不要。あなたのアイデアをカタチに！</p>
            </div>
          </div>

          <!-- card -->
          <div class="c-about">
          
            <div class="c-about__image">
              <img src="images/top_image03.png" alt="" class="c-about__image-item">
              <div class="c-about__image-shadow"></div>
            </div>

            <div class="c-about__contents">
              <strong class="c-about__contents-title">アイデアの購入</strong>
              <p class="c-about__contents-text">「投稿のネタがない...。」ときでもアイデアが見つかる</p>
            </div>
          </div>

          <!-- card -->
          <div class="c-about">
          
            <div class="c-about__image">
              <img src="images/top_image02.png" alt="" class="c-about__image-item">
              <div class="c-about__image-shadow"></div>
            </div>

            <div class="c-about__contents">
              <strong class="c-about__contents-title">レビューを確認</strong>
              <p class="c-about__contents-text">どんなアイデアか、需要があるかも事前にチェック</p>
            </div>
          </div>

        </div>
      </section>

      <!-- 実績的な部分。 -->
      <section class="p-index c-section">

        <h2 class="p-index__title">アイデアの一例</h2>
        <strong class="p-index__title-sub">IDEAS</strong>


        <div class="p-index__container">
          <!-- スライダー -->
          <swiper :options="swiperOptions">
            <swiper-slide class="c-card" v-for="idea in ideaList" :key="idea.id" style="height:auto;">
              <div class="c-card__main card-toppage">
                <img :src="idea.thumbnail" alt="" class="c-card__thumbnail card-toppage-thumbnail">
                <div class="c-card__about card-toppage-about">
                  <p class="c-card__category card-category-toppage">{{ idea.category.name }}</p>
                  <p class="c-card__title">{{ idea.title }}</p>
                  <p class="c-card__price"><span class="u-font__size-m">¥</span> {{ idea.price | numberWithCommas }}</p>
                  <div class="c-card__review">
                    <i v-for="n in 5" :key="n" class="c-card__review-icon fa-solid fa-star" :class="{ 'active': n <= idea.averageScore }"></i>
                    <a :href=" '/idea/' + idea.id + '/reviews' " class="c-card__review-link">({{ idea.review.length }})</a>
                  </div>
                  <p class="c-card__text">{{ idea.summary }}</p>
                </div>
              </div>
            </swiper-slide>
          </swiper>

        </div>

        <div class="p-index__wrap ">
          <button class="c-button">
            <a href="/index" class="p-index__link">すべてのアイデアを見る</a>
          </button>
        </div>

      </section>

      <!-- クロージング -->
      <section class="p-closing">

        <div class="p-closing__container">
          <img src="images/closing.png" alt="" class="p-closing__image">
          <img src="images/closing_sp.png" alt="" class="p-closing__image closing-sp">    

          <div class="p-closing__wrap">
            <p class="p-closing__text">アイデアの購入以外はすべて無料でご利用いただけます</p>
            <button class="c-button">
              <a href="/login" class="p-closing__link">アイデアを投稿してみる！</a>
            </button>
          </div>

        </div>
      </section>

    </div>
</template>
  
<script>
import axios from 'axios';


export default {
  
  data() {
    return {
      ideaList: [],

      // swiperの設定たち
      swiperOptions: {
        loop: true, // ループ有効
        //speed: 6000, // ループの時間
        autoplay: {
          delay: 3000, // 途切れなくループ
        },        
        slidesPerView: 1,// 一度に表示する枚数
        
        breakpoints: {
          
          420:{
            slidesPerView: 2,
            spaceBetween: 15
          },

          768:{
            slidesPerView: 3
          }
        },
      },

    }
  },

  methods: {

    // アイデア情報の取得
    async getIdeas() {
      try {
        const response = await axios.get('/api/home/ideas');
        this.ideaList = response.data.ideaList;
        console.log(response.data)
        this.getAverageScore([...this.ideaList]);
      } 
      catch (error) {
        console.log(error);
      }
    },

    // 平均評価点の取得
    async getAverageScore(ideas) {
      for (const idea of ideas) {
        try {
          const response = await axios.get('/api/idea/' + idea.id + '/average');
          idea.averageScore = response.data.averageScore;
        } catch (error) {
          console.error(error);
        }
      }
      // 平均スコアを追加した後にデータを更新（評価点に対するクラス名が反映されないため）
      this.$forceUpdate();
    },

  },


  filters: {
     
     // 値段の単位をカンマ区切りにする
     numberWithCommas(value) {
       if (value ===0) {
         return '0';
       }
       if (!value) {
         return '';
       }
       return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
     },
   },

   mounted() {
      // APIからアイデアデータを取得
      this.getIdeas();

    },

  };
</script>

<style>
  .swiper-container{
    border-radius: 5px;
  }
  /* .swiper-wrapper {
    transition-timing-function: linear;
  } */
</style>

