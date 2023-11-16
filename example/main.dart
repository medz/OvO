import 'package:ovo/ovo.dart' as ovo;

final schema = ovo.Object({
  'name': ovo.AnyOf([
    ovo.String(),
    ovo.Array(ovo.String()).unique().size(2),
  ]),
});

final data1 = {
  'name': 'John Doe',
};
final data2 = {
  'name': ['John', 'Doe'],
};

void main() async {
  print(await schema.parse(data1));
  print(await schema.parse(data2));
}
