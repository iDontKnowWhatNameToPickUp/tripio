<template>
  <div class="container">
    <div class="row d-flex justify-content-between mt-3 mb-4">
      <div class="col-md-12" style="margin-top: auto; margin-bottom: auto;">
        <div class="input-group">
          <input type="text" class="form-control searchInput" placeholder="Введите название экскурсии или города" aria-describedby="button-addon2" v-model.trim="searchText">
          <button class="btn searchButton" type="button" id="button-addon2"><i class="fas fa-search"></i></button>
        </div>
      </div>
    </div>

    <!-- карточка роута -->
    <div class="row">
      <div class="col-md-12 mb-4" v-for="route in filteredData" :key="route.id">
        <div class="card">
          <div class="card-body">
            <div class="row d-flex justify-content-between">
              <div class="col-md-4 outimg">
                <!-- <img class="innerimg" src="https://via.placeholder.com/250" alt="Изображение"> -->
                <img class="innerimg" :src="'/api/image/public/'+route.photo" alt="Изображение">
              </div>
              <div class="col-md-9">
                <div class="row">
                  <div class="col-md-9">
                    <h4 class="card-title" v-html="highlightText(route.name)"></h4>
                    <p class="card-text" v-html="highlightText($filters.truncate(route.description))"></p>
                  </div> 
                  <div class="col-md-3">
                    <div class="text-end" v-show="currentUser">
                      <button class="btn btn-favorite" @click="toggleFavorite(route)">
                        <i class="far fa-heart fa-lg"
                          :class="{'red fas': route.isLiked }"
                          ></i>
                      </button>
                    </div>
                    <p class="card-text"> <i class="fas fa-clock me-2"></i>Длительность: {{ route.duration }}</p>
                    <p class="card-text"><i class="fas fa-map-marker-alt me-2"></i>Город: {{ route.city.name }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer d-flex justify-content-between">
            <a></a>
            <!-- <a class="btn btn-outline-primary mr-2" v-on:click="more( route.id )"></a> -->
            <router-link class="btn btn-outline-primary mr-2" :to="{ name: 'MoreAboutRoute', params: { id: route.id } }"><i class="fas fa-info-circle"></i><span class="ms-1">Подробнее</span></router-link>
          </div>
        </div>
      </div>

      <div class="alert alert-danger" role="alert" v-if="errored">
        Ошибка загрузки данных!
      </div>

        <!-- прогресс бар -->
        <div class="text-center" v-if="loading">
          <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
        </div>
    </div>

    <!-- пагинация -->
    <nav>
       <ul class="pagination">
        <li class="page-item" v-if="pagination.current_page > 1">
            <a class="page-link" href="#" @click.prevent="getReadyRoutes(pagination.current_page - 1)">
                <span>Prev</span>
            </a>
        </li>
        <li class="page-item" v-for="page in pagination.last_page" :key="page" :class="[page == pagination.current_page ? 'active' : '']">
            <a class="page-link" href="#" @click.prevent="getReadyRoutes(page)">
                <span>{{ page }}</span>
            </a>
        </li>
        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
            <a class="page-link" href="#" @click.prevent="getReadyRoutes(pagination.current_page + 1)">
                <span>Next</span>
            </a>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script scoped>
export default {
  data() {
    return {
      loading: true,
      errored: false,
      data: '',
      key: 1, 
      id: '',
      city_id: '1',
      name: '',
      description: '',
      duration: '',
      file: null,
      cities: '',
      searchText: '',
      pagination: '',
      isLoggedIn: localStorage.getItem("isLoggedIn"),
      currentUser: '',
      token: localStorage.getItem("token"),
      like: ''
    }
  },
  mounted() {
    this.getReadyRoutes();
    this.getCities();
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    if(this.isLoggedIn) {
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
      axios.get('api/user').then((response) => {
        this.currentUser = response.data;
        console.log(this.currentUser);
      })
    }
  }, 
  computed: {
    filteredData() {
      const filtered = this.$options.filters.searchFilter(this.data, this.searchText);
      const filteredArray = Array.from(filtered);

      // Устанавливаем начальное значение isLiked для каждого объекта route
      filteredArray.forEach(route => {
        route.isLiked = this.isFavorite(route);
      });

      return filteredArray;
    },
  },
  filters: {
    searchFilter(data, searchText) {
      if (!searchText) return data;
      return data.filter(function(item) {
        return (
          item.name.toLowerCase().indexOf(searchText.toLowerCase()) !== -1 ||
          item.description.toLowerCase().indexOf(searchText.toLowerCase()) !== -1
        );
      });
    },
  },
  methods: {
    toggleFavorite(route) {
        const currentUserRoute = route.favorite_route.filter(item => item.user_id == this.currentUser.id)

        console.log(currentUserRoute)
        console.log(route)
        if(currentUserRoute.length > 0) {
          var favoriteId = currentUserRoute ? currentUserRoute[0].id : route.id;
          var isLiked = currentUserRoute && currentUserRoute[0].user_id === this.currentUser.id;
        } else {
          var favoriteId = route.id;
          var isLiked = false
        }
        console.log(route)
        console.log(isLiked)
        if(isLiked) { 
          axios.post('/api/favorite_routes/'+favoriteId, {
            _method: 'DELETE'
          })
          .then((response) => {
           if(response.status == 204) {
            route.isLiked = false;
            this.getReadyRoutes()
           }
          })
          .catch((error) => {
            console.error('Failde to unlike: ', error)
          })
        } else { 
          axios.post('/api/favorite_routes', {
            user_id: this.currentUser.id,
            ready_routes_id: route.id
          })
          .then((response) => {
            route.isLiked = true;
            this.getReadyRoutes()
          })
          .catch((error) => {
            console.error('Failed to like:', error);
          });
        }
      },
      isFavorite(route) {
        console.log(route)
        const currentUserRoute = route.favorite_route.filter(item => item.user_id == this.currentUser.id)
        const isLiked = currentUserRoute.some( // Check if the current user has liked the route
          favorite => favorite.user_id === this.currentUser.id
        );

        console.log(isLiked)
        return isLiked; // Return true if the route is liked by the current user, false otherwise
      },
      getReadyRoutes(page = 1) {
        axios.get('/api/routes', {
          params: {
            page: page
          }
        })
        .then( response => {
          this.data = response.data.data;
          this.pagination = response.data.meta;
        })
        .catch( error => {
            this.errored = true;
        })
        .finally( () => {
            this.loading = false;
        }); 
      },
      getCities() {
        axios.get('/api/cities')
        .then( response => {
          console.log(response.data.data)
          this.cities = response.data.data;
        })
        .catch( error => {
            this.errored = true;
        })
      },
      highlightText(text) {
        if (!this.searchText) return text;
        const regex = new RegExp(`(${this.searchText})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
      }
  }
}
</script>

<style scoped>
.btn-favorite{
  border: none;
}
.red {
  color: red;
}
</style>