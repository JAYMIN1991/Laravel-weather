<template>
  <app-layout title="Weather">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Weather
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div>
          <div class="mt-1 relative rounded-md shadow-sm">
            <input
              v-model="location.name"
              id="location"
              type="text"
              name="location"
              placeholder="Search City Here"
              :class="[
                'focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md',
                weather['cod'] === '404' ? 'border-red-500' : ''
              ]"
            >
            <div class="absolute inset-y-0 right-4 flex items-center">
              <label for="location">
                <SearchIcon class="h-5 w-5 text-indigo-500 cursor-text" />
              </label>
            </div>
          </div>
          <div>
            <span v-if="weather.cod === '404'" class="text-sm text-red-500">City not found</span>
            <span v-if="weather.cod === 200 && $page.props.errors.name" class="text-sm text-red-500">The location has already been save</span>
          </div>

          <div v-if="weather.cod === 200" class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
              <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="bg-white shadow overflow-hidden border-b border-gray-200 sm:rounded-b-lg">
                  <div class="min-w-full divide-y divide-gray-200">
                    <div
                      :class="[
                        'flex justify-between items-center cursor-pointer',
                        { 'pointer-events-none': location.processing }
                      ]"
                      @click="saveLocation"
                    >

                      <div class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 bg-orange-600 rounded-md">
                            <img :src="`http://openweathermap.org/img/wn/${weather.weather[0].icon}@2x.png`" alt="">
                          </div>
                          <div class="ml-4 text-xl">
                            {{ Math.round(weather.main.temp) }}°C
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-semibold text-gray-900">
                              {{ weather.name }}, {{ weather.sys.country }}
                            </div>
                            <div class="text-md text-gray-500">
                              {{ capitalize(weather.weather[0].description) }}
                            </div>
                            <div class="text-xs text-gray-400">
                              Feels Like {{  Math.round(weather.main.feels_like) }}°C
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="px-6 py-4 text-sm font-medium">
                        <div class="flex justify-center items-center h-full">
                          <div class="ml-3">
                            <SaveIcon class="w-5 h-5 text-indigo-600" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 bg-white overflow-hidden shadow-lg sm:rounded-lg">
          <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
            <div class="flex flex-col">
              <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                  <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <div class="min-w-full divide-y divide-gray-200">
                      <div class="flex justify-between items-center" v-for="(city, index) in weathers" :key="index">
                        <div class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                              <div class="flex justify-center items-center w-10 h-10 rounded-md bg-orange-600">
                                <img :src="`http://openweathermap.org/img/wn/${city.weather[0].icon}@2x.png`" alt="">
                              </div>
                            </div>
                            <div class="ml-4 text-xl">
                              {{ Math.round(city.main.temp) }}°C
                            </div>
                            <div class="ml-4">
                              <div class="text-sm font-semibold text-gray-900">
                                {{ city.name }}, {{ city.sys.country }}
                              </div>
                              <div class="text-md text-gray-500">
                                {{ capitalize(city.weather[0].description) }}
                              </div>
                              <div class="text-xs text-gray-400">
                                Feels Like {{  Math.round(city.main.feels_like) }}°C
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="px-6 py-4 text-sm font-medium">
                          <div class="flex justify-center items-center h-full">
                            <div>
                              <Link :href="`/location/${city.city.id}`" method="delete" as="button" type="button">
                                <TrashIcon class="w-5 h-5 text-red-600" />
                              </Link>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end" v-if="locations.data.length">
          <nav class="relative mt-6 z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
            <Component
              :is="link.url ? 'Link' : 'span'"
              v-for="(link, index) in locations.links"
              :key="index"
              :href="link.url"
              v-html="link.label"
              :class="[
                'bg-white border-gray-300 text-gray-500',
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                 link.url ? 'hover:bg-gray-50' : 'bg-gray-100 cursor-not-allowed'
              ]"
            />
          </nav>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent, ref, onMounted, watch, nextTick } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { BeakerIcon, PencilAltIcon, SaveIcon, SearchIcon, TrashIcon } from '@heroicons/vue/outline'
import { useForm, Link } from '@inertiajs/inertia-vue3'

export default defineComponent({
  components: {
    // components
    AppLayout,
    Link,

    // icons
    BeakerIcon,
    PencilAltIcon,
    SaveIcon,
    SearchIcon,
    TrashIcon,
  },

  props: {
    locations: Object
  },

  setup(props) {
    const location = useForm({
      name: ''
    })
    const weather = ref(false)
    const weathers = ref([])

    async function fetchData() {
      const response = await axios.get(`/api/weather?location=${location.name}`)
      weather.value = response.data
    }
    
    async function fetchLocationsData() {
      props.locations.data.forEach(async (city, index) => {
        const response = await axios.get(`/api/weather?location=${city.name}`)
        
         nextTick(() => weathers.value[index] = { ...response.data, ...{ city }})
      })
    }

    function capitalize(word) {
      return word[0].toUpperCase() + word.slice(1)
    }

    function saveLocation() {
      location.post('/location')
    }

    watch(location, () => fetchData())

    onMounted(() => {
      fetchData()
      fetchLocationsData()
    })

    return {
      location,
      weather,
      weathers,
      capitalize,
      saveLocation,
    }
  }
})
</script>
