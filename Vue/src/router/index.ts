import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../layout/AppLayout.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: AppLayout,
            children: [
                {
                    path: '/',
                    name: 'home',
                    component: () => import('@/views/Home.vue')
                },
                {
                    path: '/dashboard',
                    name: 'dashboard',
                    component: () => import('@/views/pages/Dashboard.vue')
                },
                {
                    path: '/items/hotel',
                    name: 'hotels',
                    component: () => import('@/views/pages/hotel/Hotel.vue'),
                },
                {
                    path: '/items/hotel/:id',
                    name: 'hotelDetails',
                    component: () => import('@/views/pages/hotel/HotelDetails.vue'),
                    props: true
                },
                {
                    path: '/items/hotel/:id/services',
                    name: 'hotelServices',
                    component: () => import('@/views/pages/hotel/HotelServices.vue'),
                    props: true
                },
                {
                    path: '/items/guest',
                    name: 'guests',
                    component: () => import('@/views/pages/guest/Guest.vue')
                },
                {
                    path: '/items/guest/:id/services',
                    name: 'guestServices',
                    component: () => import('@/views/pages/guest/GuestServices.vue'),
                    props: true
                },
                {
                    path: '/items/guest/:id/rooms',
                    name: 'guestRooms',
                    component: () => import('@/views/pages/guest/GuestRooms.vue'),
                    props: true
                },
                {
                    path: '/items/room',
                    name: 'rooms',
                    component: () => import('@/views/pages/room/Room.vue')
                },
                {
                    path: '/items/room/:id/guests',
                    name: 'roomGuests',
                    component: () => import('@/views/pages/room/RoomGuests.vue'),
                    props: true
                },
                {
                    path: '/items/type',
                    name: 'types',
                    component: () => import('@/views/pages/type/Type.vue')
                },
                {
                    path: '/items/service',
                    name: 'services',
                    component: () => import('@/views/pages/service/Service.vue')
                },
                {
                    path: '/items/service/:id/hotels',
                    name: 'serviceHotels',
                    component: () => import('@/views/pages/service/ServiceHotels.vue'),
                    props: true
                },
                {
                    path: '/items/service/:id/guests',
                    name: 'serviceGuests',
                    component: () => import('@/views/pages/service/ServiceGuests.vue'),
                    props: true
                },
                {
                    path: '/items/category',
                    name: 'categories',
                    component: () => import('@/views/pages/category/Category.vue')
                },
                {
                    path: '/uikit/formlayout',
                    name: 'formlayout',
                    component: () => import('@/views/uikit/FormLayout.vue')
                },
                {
                    path: '/uikit/input',
                    name: 'input',
                    component: () => import('@/views/uikit/Input.vue')
                },
                {
                    path: '/uikit/floatlabel',
                    name: 'floatlabel',
                    component: () => import('@/views/uikit/FloatLabel.vue')
                },
                {
                    path: '/uikit/invalidstate',
                    name: 'invalidstate',
                    component: () => import('@/views/uikit/InvalidState.vue')
                },
                {
                    path: '/uikit/button',
                    name: 'button',
                    component: () => import('@/views/uikit/Button.vue')
                },
                {
                    path: '/uikit/table',
                    name: 'table',
                    component: () => import('@/views/uikit/Table.vue')
                },
                {
                    path: '/uikit/list',
                    name: 'list',
                    component: () => import('@/views/uikit/List.vue')
                },
                {
                    path: '/uikit/tree',
                    name: 'tree',
                    component: () => import('@/views/uikit/Tree.vue')
                },
                {
                    path: '/uikit/panel',
                    name: 'panel',
                    component: () => import('@/views/uikit/Panels.vue')
                },

                {
                    path: '/uikit/overlay',
                    name: 'overlay',
                    component: () => import('@/views/uikit/Overlay.vue')
                },
                {
                    path: '/uikit/media',
                    name: 'media',
                    component: () => import('@/views/uikit/Media.vue')
                },
                {
                    path: '/uikit/menu',
                    component: () => import('@/views/uikit/Menu.vue'),
                    children: [
                        {
                            path: '/uikit/menu',
                            component: () => import('@/views/uikit/menu/PersonalDemo.vue')
                        },
                        {
                            path: '/uikit/menu/seat',
                            component: () => import('@/views/uikit/menu/SeatDemo.vue')
                        },
                        {
                            path: '/uikit/menu/payment',
                            component: () => import('@/views/uikit/menu/PaymentDemo.vue')
                        },
                        {
                            path: '/uikit/menu/confirmation',
                            component: () => import('@/views/uikit/menu/ConfirmationDemo.vue')
                        }
                    ]
                },
                {
                    path: '/uikit/message',
                    name: 'message',
                    component: () => import('@/views/uikit/Messages.vue')
                },
                {
                    path: '/uikit/file',
                    name: 'file',
                    component: () => import('@/views/uikit/File.vue')
                },
                {
                    path: '/uikit/charts',
                    name: 'charts',
                    component: () => import('@/views/uikit/Chart.vue')
                },
                {
                    path: '/uikit/misc',
                    name: 'misc',
                    component: () => import('@/views/uikit/Misc.vue')
                },
                {
                    path: '/blocks',
                    name: 'blocks',
                    component: () => import('@/views/utilities/Blocks.vue')
                },
                {
                    path: '/utilities/icons',
                    name: 'icons',
                    component: () => import('@/views/utilities/Icons.vue')
                },
                {
                    path: '/pages/timeline',
                    name: 'timeline',
                    component: () => import('@/views/pages/Timeline.vue')
                },
                {
                    path: '/pages/empty',
                    name: 'empty',
                    component: () => import('@/views/pages/Empty.vue')
                },
                {
                    path: '/pages/crud',
                    name: 'crud',
                    component: () => import('@/views/pages/Crud.vue')
                },
                {
                    path: '/documentation',
                    name: 'documentation',
                    component: () => import('@/views/utilities/Documentation.vue')
                }
            ]
        },
        {
            path: '/landing',
            name: 'landing',
            component: () => import('@/views/pages/Landing.vue')
        },
        {
            path: '/pages/notfound',
            name: 'notfound',
            component: () => import('@/views/pages/NotFound.vue')
        },

        {
            path: '/auth/login',
            name: 'login',
            component: () => import('@/views/pages/auth/Login.vue')
        },
        {
            path: '/auth/access',
            name: 'accessDenied',
            component: () => import('@/views/pages/auth/Access.vue')
        },
        {
            path: '/auth/error',
            name: 'error',
            component: () => import('@/views/pages/auth/Error.vue')
        }
    ]
});

export default router;
