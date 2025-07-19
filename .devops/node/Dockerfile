FROM node:22-alpine as main

WORKDIR /app

COPY ./front/package*.json ./

RUN npm install

FROM main as builder

COPY ./front /app

ARG VITE_API_BASE_URL
ARG VITE_MATOMO_URL
ARG VITE_MATOMO_SITE_ID

ENV VITE_API_BASE_URL=$VITE_API_BASE_URL
ENV VITE_MATOMO_URL=$VITE_MATOMO_URL
ENV VITE_MATOMO_SITE_ID=$VITE_MATOMO_SITE_ID

RUN npm install
RUN npx tailwindcss -i src/assets/main.css -o src/assets/main-build.css
RUN npm run build

FROM main as dev

COPY ./front /app

COPY --from=main /app/node_modules /app

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]

FROM nginx:1.25-alpine as prod

COPY ./.devops/node/nginx.conf /etc/nginx/conf.d/default.conf

COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

