#!/usr/bin/env sh

registry=${REGISTRY:-docker.io}
cache_repo=${CACHE_REPO:-tbaile/lupus-cache}
tag=${TAG:-develop}

build_and_push() {
    if [ -z "$1" ]; then
        echo "No tag name provided"
        exit 1
    fi
    if [ -z "$2" ]; then
        echo "No containerfile provided"
        exit 1
    fi
    buildah build --manifest "$registry"/"$1":"$tag" \
        --file "$2" \
        --target production \
        --platform linux/arm64 \
        --platform linux/amd64 \
        --layers \
        --cache-from "$registry/$cache_repo" \
        --cache-to "$registry/$cache_repo" \
        --jobs 0 \
	--force-rm
    podman manifest push --all "$registry"/"$1":"$tag" docker://"$registry"/"$1":"$tag"
}

if [ -z "$1" ]; then
    echo "No action specified."
    echo "Available actions are: 'build', 'test' and 'develop'."
    exit 1
else
    case "$1" in
    build)
        echo "Build and push images."
        build_and_push "tbaile/lupus-app" "containers/php/Containerfile" &
        build_and_push "tbaile/lupus-web" "containers/nginx/Containerfile" &
        wait
        echo "Build and push successful."
        ;;
    test)
        echo "Building test images."
        buildah build --file "containers/php/Containerfile" \
            --target testing \
            --platform linux/arm64 \
            --platform linux/amd64 \
            --layers \
            --cache-from "$registry/$cache_repo" \
            --jobs 0 \
            --force-rm
        echo "Build test images successful."
        ;;
    develop)
        echo "Build local testing images."
        buildah build --file "containers/php/Containerfile" \
            --target production \
            --platform linux/amd64 \
            --layers \
            --cache-from "$registry/$cache_repo" \
            --jobs 0 \
            --tag "$registry/tbaile/lupus-app:develop" \
            --force-rm &
        buildah build --file "containers/nginx/Containerfile" \
            --target production \
            --platform linux/amd64 \
            --layers \
            --cache-from "$registry/$cache_repo" \
            --jobs 0 \
            --tag "$registry/tbaile/lupus-web:develop" \
            --force-rm &
        wait
        echo "Build local testing images successful."
        ;;
    *)
        echo "Unknown action: $1"
        exit 1
        ;;
    esac
fi
