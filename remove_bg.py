import sys, os
from rembg import remove, new_session

def remove_background(input_path, output_path):
    try:
        # Load u2net model from U2NET_HOME
        session = new_session("u2net")

        with open(input_path, "rb") as i:
            input_data = i.read()
            output_data = remove(input_data, session=session)

            with open(output_path, "wb") as o:
                o.write(output_data)

        print(f"Background removal complete. Saved to {output_path}")

    except Exception as e:
        print(f"Error during background removal: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python remove_bg.py <input_file> <output_file>", file=sys.stderr)
        sys.exit(1)

    input_file = sys.argv[1]
    output_file = sys.argv[2]

    if not os.path.exists(input_file):
        print(f"Error: Input file '{input_file}' not found.", file=sys.stderr)
        sys.exit(1)

    remove_background(input_file, output_file)
