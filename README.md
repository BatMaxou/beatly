# Beatly

## Installation

```bash
git clone git@github.com:BatMaxou/beatly.git
```

Copy `front/.env.local.example` to `front/.env.local` and fill in / modify the values

```bash
cp front/.env.local.example front/.env.local
```

Copy `api/.env.local.example` to `api/.env.local` and fill in / modify the values

```bash
cp api/.env.local.example api/.env.local
```

Copy `compose.override.example.yaml` to `compose.override.yaml` and fill in / modify the configuration

```bash
cp compose.override.example.yaml compose.override.yaml
```

## Init (dev)

```bash
make install
```

## Run

```bash
make up
```

## Stop

```bash
make down
```
