FROM node:22-alpine as main

WORKDIR /app

COPY ./front/package*.json ./

RUN npm install

FROM main as builder

COPY ./front /app

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
COPY --from=builder /app/assets/main-build.css /usr/share/nginx/html/assets/main-build.css

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

